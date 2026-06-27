<?php

namespace App\Services;

use App\Models\WhatsAppMessage;
use App\Models\User;
use App\Models\StudentReport;
use App\Support\WhatsAppBridgeSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $apiUrl;
    private $apiToken;
    private $phoneNumberId;
    private $localApiUrl;

    public function __construct(
        private ?WhatsAppBridgeService $bridge = null,
        private ?WhatsAppPacingService $pacing = null
    ) {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v18.0');
        $this->apiToken = config('services.whatsapp.api_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->localApiUrl = WhatsAppBridgeSettings::bridgeUrl()
            ?: config('services.whatsapp.local_api_url', 'http://localhost:3001');
        $this->bridge ??= app(WhatsAppBridgeService::class);
        $this->pacing ??= app(WhatsAppPacingService::class);
    }

    protected function serviceType(): string
    {
        return WhatsAppBridgeSettings::serviceType();
    }

    /**
     * إرسال رسالة واتساب
     */
    public function sendMessage(string $phoneNumber, string $message, string $type = 'text', array $options = [])
    {
        try {
            $actorId = $options['user_id'] ?? auth()->id();
            $batchId = $options['batch_id'] ?? null;

            // تنسيق رقم الهاتف (إضافة رمز الدولة إذا لم يكن موجوداً)
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            // التحقق من نوع الخدمة
            $serviceType = $this->serviceType();
            
            if ($serviceType === 'disabled') {
                // وضع التجربة - حفظ في قاعدة البيانات فقط
                $whatsappMessage = WhatsAppMessage::create([
                    'user_id' => $actorId,
                    'phone_number' => $formattedPhone,
                    'message' => $message,
                    'type' => $type,
                    'status' => 'sent',
                    'response_data' => ['test_mode' => true, 'message' => 'تم الحفظ في وضع التجربة'],
                    'sent_at' => now(),
                ]);

                return [
                    'success' => true,
                    'message_id' => $whatsappMessage->id,
                    'test_mode' => true
                ];
            } elseif (in_array($serviceType, ['local', 'wwebjs'], true)) {
                return $this->sendViaBridge($formattedPhone, $message, $type, $actorId, $batchId);
            } elseif ($serviceType === 'custom') {
                // استخدام API مخصص من المستخدم
                return $this->sendViaCustomAPI($formattedPhone, $message, $type);
            }

            // التحقق من صحة الإعدادات
            if (!$this->apiToken || !$this->phoneNumberId) {
                throw new \Exception('إعدادات WhatsApp API غير مكتملة');
            }

            $response = Http::withToken($this->apiToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $formattedPhone,
                    'type' => $type,
                    'text' => [
                        'body' => $message
                    ]
                ]);

            $responseData = $response->json();

            // تسجيل الرسالة في قاعدة البيانات
            $whatsappMessage = WhatsAppMessage::create([
                'user_id' => $actorId,
                'phone_number' => $formattedPhone,
                'message' => $message,
                'type' => $type,
                'status' => $response->successful() ? 'sent' : 'failed',
                'response_data' => $responseData,
                'sent_at' => now(),
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'phone' => $formattedPhone,
                    'message_id' => $whatsappMessage->id,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message_id' => $whatsappMessage->id,
                    'whatsapp_id' => $responseData['messages'][0]['id'] ?? null
                ];
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $formattedPhone,
                    'error' => $responseData,
                    'message_id' => $whatsappMessage->id
                ]);

                return [
                    'success' => false,
                    'error' => $responseData['error']['message'] ?? 'خطأ غير معروف'
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'خطأ في الاتصال بخدمة الواتساب'
            ];
        }
    }

    /**
     * إرسال عبر whatsapp-web.js Bridge (Node.js خارج Shared Hosting)
     */
    private function sendViaBridge(string $phoneNumber, string $message, string $type = 'text', ?int $actorId = null, ?int $batchId = null)
    {
        try {
            $actorId ??= auth()->id();

            if ($limitError = $this->pacing->assertCanSend()) {
                WhatsAppMessage::create([
                    'user_id' => $actorId,
                    'phone_number' => $phoneNumber,
                    'message' => $message,
                    'type' => $type,
                    'status' => 'failed',
                    'error_message' => $limitError,
                ]);

                return ['success' => false, 'error' => $limitError];
            }

            $ready = $this->bridge->ensureReadyForSend();
            if (! ($ready['success'] ?? false)) {
                $blockError = $ready['error'] ?? 'الواتساب غير جاهز للإرسال.';

                WhatsAppMessage::create([
                    'user_id' => $actorId,
                    'phone_number' => $phoneNumber,
                    'message' => $message,
                    'type' => $type,
                    'status' => 'failed',
                    'error_message' => $blockError,
                ]);

                return ['success' => false, 'error' => $blockError];
            }

            $this->pacing->waitBeforeSend($batchId);

            $result = $this->bridge->sendMessage(
                $phoneNumber,
                $message,
                $this->pacing->simulateTyping()
            );
            $responseData = $result['data'] ?? [];

            $whatsappMessage = WhatsAppMessage::create([
                'user_id' => $actorId,
                'phone_number' => $phoneNumber,
                'message' => $message,
                'type' => $type,
                'status' => ($result['success'] ?? false) ? 'sent' : 'failed',
                'response_data' => $responseData,
                'whatsapp_message_id' => $responseData['message_id'] ?? null,
                'sent_at' => ($result['success'] ?? false) ? now() : null,
                'error_message' => ! ($result['success'] ?? false) ? ($result['error'] ?? 'خطأ غير معروف') : null,
            ]);

            if ($result['success'] ?? false) {
                $this->pacing->recordSuccessfulSend();

                Log::info('WhatsApp message sent via bridge', [
                    'phone' => $phoneNumber,
                    'message_id' => $whatsappMessage->id,
                ]);

                return [
                    'success' => true,
                    'message_id' => $whatsappMessage->id,
                    'bridge' => true,
                ];
            }

            Log::error('Failed to send WhatsApp message via bridge', [
                'phone' => $phoneNumber,
                'error' => $result['error'] ?? 'unknown',
                'message_id' => $whatsappMessage->id,
            ]);

            return [
                'success' => false,
                'error' => $result['error'] ?? 'خطأ في جسر الواتساب',
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp bridge error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            WhatsAppMessage::create([
                'user_id' => $actorId ?? auth()->id(),
                'phone_number' => $phoneNumber,
                'message' => $message,
                'type' => $type,
                'status' => 'failed',
                'error_message' => 'خطأ في الاتصال بالجسر: ' . $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'خطأ في الاتصال بجسر الواتساب',
            ];
        }
    }

    /**
     * @deprecated استخدم sendViaBridge
     */
    private function sendViaLocalService(string $phoneNumber, string $message, string $type = 'text')
    {
        return $this->sendViaBridge($phoneNumber, $message, $type, auth()->id());
    }

    /**
     * إرسال عبر API مخصص
     */
    private function sendViaCustomAPI(string $phoneNumber, string $message, string $type = 'text')
    {
        try {
            $apiUrl = config('services.whatsapp.api_url');
            $apiToken = config('services.whatsapp.api_token');
            $method = config('services.whatsapp.request_method', 'POST');
            $phoneParam = config('services.whatsapp.phone_param', 'phone');
            $messageParam = config('services.whatsapp.message_param', 'message');
            $extraParams = json_decode(config('services.whatsapp.extra_params', '{}'), true);

            // إعداد البيانات
            $data = array_merge($extraParams, [
                $phoneParam => $phoneNumber,
                $messageParam => $message,
            ]);

            // إعداد Headers
            $headers = [
                'Content-Type' => 'application/json',
            ];

            if ($apiToken) {
                $headers['Authorization'] = 'Bearer ' . $apiToken;
            }

            // إرسال الطلب
            if ($method === 'POST') {
                $response = Http::withHeaders($headers)->timeout(30)->post($apiUrl, $data);
            } else {
                $response = Http::withHeaders($headers)->timeout(30)->get($apiUrl, $data);
            }

            $responseData = $response->json();

            // تسجيل الرسالة
            $whatsappMessage = WhatsAppMessage::create([
                'user_id' => auth()->id(),
                'phone_number' => $phoneNumber,
                'message' => $message,
                'type' => $type,
                'status' => $response->successful() ? 'sent' : 'failed',
                'response_data' => $responseData,
                'sent_at' => $response->successful() ? now() : null,
                'error_message' => !$response->successful() ? 'Custom API Error: ' . $response->status() : null,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $whatsappMessage->id,
                    'custom_api' => true
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'خطأ في Custom API: ' . $response->status()
                ];
            }

        } catch (\Exception $e) {
            // حفظ كرسالة فاشلة
            WhatsAppMessage::create([
                'user_id' => auth()->id(),
                'phone_number' => $phoneNumber,
                'message' => $message,
                'type' => $type,
                'status' => 'failed',
                'error_message' => 'Custom API Error: ' . $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'خطأ في Custom API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * إرسال تقرير شهري لولي الأمر
     */
    public function sendMonthlyReport(User $parent, User $student, array $reportData)
    {
        $report = $this->generateMonthlyReportText($student, $reportData);
        
        // إرسال الرسالة
        $result = $this->sendMessage($parent->phone, $report, 'text');

        // تسجيل التقرير
        if ($result['success']) {
            StudentReport::create([
                'student_id' => $student->id,
                'parent_id' => $parent->id,
                'report_month' => now()->format('Y-m'),
                'report_data' => $reportData,
                'sent_via' => 'whatsapp',
                'sent_at' => now(),
                'status' => 'sent'
            ]);
        }

        return $result;
    }

    /**
     * إرسال رسالة للطالب
     */
    public function sendStudentMessage(User $student, string $message, string $type = 'academic')
    {
        return $this->sendMessage($student->phone, $message, 'text', [
            'student_id' => $student->id,
            'message_type' => $type
        ]);
    }

    /**
     * إرسال رسالة جماعية للطلاب
     */
    public function sendBulkMessage(array $students, string $message, string $type = 'announcement')
    {
        $results = [];
        
        foreach ($students as $student) {
            $result = $this->sendStudentMessage($student, $message, $type);
            $results[] = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'success' => $result['success'],
                'error' => $result['error'] ?? null
            ];
        }

        return $results;
    }

    /**
     * تنسيق رقم الهاتف
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // إزالة المسافات والرموز
        $phone = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // إضافة رمز مصر إذا لم يكن موجوداً
        if (!str_starts_with($phone, '+') && !str_starts_with($phone, '20')) {
            if (str_starts_with($phone, '0')) {
                $phone = '20' . substr($phone, 1);
            } else {
                $phone = '20' . $phone;
            }
        }

        // إزالة + إذا كان موجوداً
        $phone = ltrim($phone, '+');

        return $phone;
    }

    /**
     * توليد نص التقرير الشهري
     */
    private function generateMonthlyReportText(User $student, array $reportData): string
    {
        $month = now()->format('F Y');
        $monthArabic = now()->locale('ar')->format('F Y');

        $report = "📊 *تقرير شهري - {$monthArabic}*\n\n";
        $report .= "👤 *الطالب:* {$student->name}\n";
        $report .= "📞 *رقم الهاتف:* {$student->phone}\n\n";

        // إحصائيات الكورسات
        if (isset($reportData['courses'])) {
            $report .= "📚 *الكورسات المسجل بها:*\n";
            foreach ($reportData['courses'] as $course) {
                $progressPercent = $course['progress_percentage'] ?? 0;
                $report .= "  • {$course['title']}: {$progressPercent}%\n";
            }
            $report .= "\n";
        }

        // إحصائيات الامتحانات
        if (isset($reportData['exams'])) {
            $report .= "🎯 *نتائج الامتحانات:*\n";
            foreach ($reportData['exams'] as $exam) {
                $score = $exam['score'] ?? 0;
                $totalMarks = $exam['total_marks'] ?? 100;
                $percentage = $exam['percentage'] ?? 0;
                $status = $exam['status'] ?? 'لم يؤدِ';
                
                $report .= "  • {$exam['title']}: {$score}/{$totalMarks} ({$percentage}%) - {$status}\n";
            }
            $report .= "\n";
        }

        // إحصائيات مشاهدة الفيديوهات
        if (isset($reportData['videos'])) {
            $totalWatched = $reportData['videos']['total_watched'] ?? 0;
            $totalWatchTime = $reportData['videos']['total_watch_time'] ?? 0;
            $watchTimeFormatted = $this->formatMinutes($totalWatchTime);
            
            $report .= "📹 *مشاهدة الفيديوهات:*\n";
            $report .= "  • عدد الفيديوهات المشاهدة: {$totalWatched}\n";
            $report .= "  • إجمالي وقت المشاهدة: {$watchTimeFormatted}\n\n";
        }

        // الحضور والنشاط
        if (isset($reportData['attendance'])) {
            $activeDays = $reportData['attendance']['active_days'] ?? 0;
            $lastLogin = $reportData['attendance']['last_login'] ?? 'غير معروف';
            
            $report .= "📅 *الحضور والنشاط:*\n";
            $report .= "  • الأيام النشطة هذا الشهر: {$activeDays} يوم\n";
            $report .= "  • آخر دخول: {$lastLogin}\n\n";
        }

        // التقييم العام
        if (isset($reportData['overall'])) {
            $overallGrade = $reportData['overall']['grade'] ?? 'غير متاح';
            $recommendation = $reportData['overall']['recommendation'] ?? '';
            
            $report .= "⭐ *التقييم العام:* {$overallGrade}\n";
            if ($recommendation) {
                $report .= "💡 *التوصيات:* {$recommendation}\n";
            }
        }

        $report .= "\n📱 *منصة مستر طارق الداجن*\n";
        $report .= "للاستفسارات: اتصل بنا\n";
        $report .= "🌐 " . config('app.url');

        return $report;
    }

    /**
     * تحويل الدقائق إلى تنسيق مقروء
     */
    private function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} دقيقة";
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes > 0) {
            return "{$hours} ساعة و {$remainingMinutes} دقيقة";
        }
        
        return "{$hours} ساعة";
    }

    /**
     * الحصول على بيانات التقرير للطالب
     */
    public function generateStudentReportData(User $student): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // الكورسات المسجل بها
        $courses = $student->courseEnrollments()
            ->with(['course'])
            ->get()
            ->map(function ($enrollment) {
                return [
                    'title' => $enrollment->course->title,
                    'progress_percentage' => $enrollment->progress ?? 0,
                    'status' => $enrollment->status,
                ];
            });

        // الامتحانات هذا الشهر
        $exams = $student->examAttempts()
            ->with(['exam'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->map(function ($attempt) {
                return [
                    'title' => $attempt->exam->title,
                    'score' => $attempt->score,
                    'total_marks' => $attempt->exam->total_marks,
                    'percentage' => $attempt->percentage,
                    'status' => $attempt->result_status,
                    'date' => $attempt->created_at->format('d/m/Y'),
                ];
            });

        // إحصائيات الفيديوهات (إذا كان النموذج موجوداً)
        $videoStats = [
            'total_watched' => 0,
            'total_watch_time' => 0,
        ];

        // الحضور والنشاط
        $attendanceStats = [
            'active_days' => $this->getActiveDaysCount($student, $startOfMonth, $endOfMonth),
            'last_login' => $student->last_login_at ? $student->last_login_at->format('d/m/Y H:i') : 'غير معروف',
        ];

        // التقييم العام
        $averageScore = $exams->avg('percentage') ?? 0;
        $overallGrade = $this->getGradeFromPercentage($averageScore);
        $recommendation = $this->getRecommendation($averageScore, $courses->count());

        return [
            'courses' => $courses->toArray(),
            'exams' => $exams->toArray(),
            'videos' => $videoStats,
            'attendance' => $attendanceStats,
            'overall' => [
                'grade' => $overallGrade,
                'recommendation' => $recommendation,
                'average_score' => round($averageScore, 1),
            ]
        ];
    }

    /**
     * الحصول على عدد الأيام النشطة
     */
    private function getActiveDaysCount(User $student, $startDate, $endDate): int
    {
        // يمكن تحسين هذا بناءً على جدول activity_logs
        return \App\Models\ActivityLog::where('user_id', $student->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->count();
    }

    /**
     * الحصول على التقدير من النسبة المئوية
     */
    private function getGradeFromPercentage(float $percentage): string
    {
        if ($percentage >= 90) return 'ممتاز';
        if ($percentage >= 80) return 'جيد جداً';
        if ($percentage >= 70) return 'جيد';
        if ($percentage >= 60) return 'مقبول';
        if ($percentage >= 50) return 'ضعيف';
        return 'راسب';
    }

    /**
     * الحصول على التوصيات
     */
    private function getRecommendation(float $averageScore, int $coursesCount): string
    {
        if ($averageScore >= 80) {
            return 'أداء ممتاز! استمر في هذا المستوى الرائع.';
        } elseif ($averageScore >= 60) {
            return 'أداء جيد، يمكن تحسينه بمزيد من المراجعة والممارسة.';
        } elseif ($averageScore >= 40) {
            return 'يحتاج إلى مزيد من الاهتمام والمتابعة في الدراسة.';
        } else {
            return 'يتطلب متابعة مكثفة وحضور دروس إضافية.';
        }
    }

    /**
     * إرسال تقرير فوري للطالب
     */
    public function sendStudentProgress(User $student, ?string $courseTitle = null): array
    {
        $reportData = $this->generateStudentReportData($student);
        
        $message = "📊 *تقرير تقدمك الدراسي*\n\n";
        $message .= "👤 *مرحباً {$student->name}*\n\n";

        if ($courseTitle) {
            $message .= "📚 *الكورس:* {$courseTitle}\n\n";
        }

        // آخر النتائج
        if (!empty($reportData['exams'])) {
            $lastExam = end($reportData['exams']);
            $message .= "🎯 *آخر امتحان:*\n";
            $message .= "  • {$lastExam['title']}\n";
            $message .= "  • النتيجة: {$lastExam['score']}/{$lastExam['total_marks']} ({$lastExam['percentage']}%)\n";
            $message .= "  • الحالة: {$lastExam['status']}\n\n";
        }

        $message .= "📈 *متوسط درجاتك:* {$reportData['overall']['average_score']}%\n";
        $message .= "⭐ *تقييمك العام:* {$reportData['overall']['grade']}\n\n";
        $message .= "💡 *نصيحة:* {$reportData['overall']['recommendation']}\n\n";
        $message .= "🎓 *استمر في التقدم!*\n";
        $message .= "📱 منصة مستر طارق الداجن";

        return $this->sendMessage($student->phone, $message);
    }

    /**
     * إرسال إشعار عن نتيجة امتحان
     */
    public function sendExamResult(User $student, $examAttempt): array
    {
        $exam = $examAttempt->exam;
        $score = $examAttempt->score;
        $totalMarks = $exam->total_marks;
        $percentage = $examAttempt->percentage;
        $status = $examAttempt->result_status;

        $message = "🎯 *نتيجة امتحان جديدة*\n\n";
        $message .= "👤 *الطالب:* {$student->name}\n";
        $message .= "📝 *الامتحان:* {$exam->title}\n";
        $message .= "📊 *النتيجة:* {$score}/{$totalMarks} ({$percentage}%)\n";
        $message .= "✅ *الحالة:* {$status}\n";
        $message .= "📅 *التاريخ:* " . $examAttempt->submitted_at->format('d/m/Y H:i') . "\n\n";

        if ($percentage >= $exam->passing_marks) {
            $message .= "🎉 *مبروك! نجحت في الامتحان*\n";
        } else {
            $message .= "📖 *استمر في الدراسة والمراجعة*\n";
        }

        $message .= "\n📱 منصة مستر طارق الداجن";

        return $this->sendMessage($student->phone, $message);
    }
}

<?php

namespace App\Services;

use App\Models\WhatsAppMessage;
use App\Models\User;
use App\Models\StudentReport;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function __construct(
        private ?WhatsAppCloudService $cloud = null,
        private ?WhatsAppPacingService $pacing = null
    ) {
        $this->cloud ??= app(WhatsAppCloudService::class);
        $this->pacing ??= app(WhatsAppPacingService::class);
    }

    protected function serviceType(): string
    {
        return WhatsAppCloudSettings::serviceType();
    }

    /**
     * إرسال رسالة واتساب
     */
    public function sendMessage(string $phoneNumber, string $message, string $type = 'text', array $options = [])
    {
        try {
            $actorId = $options['user_id'] ?? auth()->id();
            $batchId = $options['batch_id'] ?? null;
            $skipReadyCheck = (bool) ($options['skip_ready_check'] ?? false);
            $forceOfficial = (bool) ($options['force_official'] ?? false);

            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            $serviceType = $this->serviceType();

            if ($serviceType === 'disabled' && ! $forceOfficial) {
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
            }

            if (! $skipReadyCheck) {
                $ready = $this->cloud->canSendNow();
                if (! ($ready['success'] ?? false)) {
                    WhatsAppMessage::create([
                        'user_id' => $actorId,
                        'phone_number' => $formattedPhone,
                        'message' => $message,
                        'type' => $type,
                        'status' => 'failed',
                        'error_message' => $ready['error'] ?? 'الواتساب غير جاهز',
                    ]);

                    return ['success' => false, 'error' => $ready['error'] ?? 'الواتساب غير جاهز'];
                }
            }

            if ($limitError = $this->pacing->assertCanSend()) {
                WhatsAppMessage::create([
                    'user_id' => $actorId,
                    'phone_number' => $formattedPhone,
                    'message' => $message,
                    'type' => $type,
                    'status' => 'failed',
                    'error_message' => $limitError,
                ]);

                return ['success' => false, 'error' => $limitError];
            }

            $this->pacing->waitBeforeSend($batchId);

            $creds = $this->cloud->resolveCredentials();
            $apiUrl = WhatsAppCloudSettings::apiUrl();
            $apiToken = $creds['access_token'];
            $phoneNumberId = $creds['phone_number_id'];

            if ($apiToken === '' || $phoneNumberId === '') {
                throw new \Exception('إعدادات WhatsApp Cloud API غير مكتملة');
            }

            $response = Http::withToken($apiToken)
                ->timeout(60)
                ->post("{$apiUrl}/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $formattedPhone,
                    'type' => $type,
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            $responseData = $response->json() ?? [];
            $metaError = is_array($responseData['error'] ?? null) ? $responseData['error'] : null;
            $waMessageId = $responseData['messages'][0]['id'] ?? null;
            $accepted = $response->successful() && is_string($waMessageId) && $waMessageId !== '';

            $errorText = $accepted
                ? null
                : $this->cloud->humanizeSendError(
                    $metaError,
                    (string) ($metaError['message'] ?? 'فشل إرسال الرسالة')
                );

            $whatsappMessage = WhatsAppMessage::create([
                'user_id' => $actorId,
                'phone_number' => $formattedPhone,
                'message' => $message,
                'type' => $type,
                'status' => $accepted ? 'sent' : 'failed',
                'response_data' => $responseData,
                'whatsapp_message_id' => $waMessageId,
                'sent_at' => $accepted ? now() : null,
                'error_message' => $errorText,
            ]);

            if ($accepted) {
                app(WhatsAppInboxService::class)->mirrorOutboundWhatsAppMessage($whatsappMessage);
            }

            if ($accepted) {
                Log::info('WhatsApp Cloud message accepted by Meta', [
                    'phone' => $formattedPhone,
                    'message_id' => $whatsappMessage->id,
                    'whatsapp_id' => $waMessageId,
                ]);

                return [
                    'success' => true,
                    'accepted_by_meta' => true,
                    'message_id' => $whatsappMessage->id,
                    'whatsapp_id' => $waMessageId,
                    'notice' => 'قُبلت الرسالة من Meta. إن لم تصل للمستلم خلال دقائق، راجع سجل الرسائل — الرسائل النصية الحرة تعمل فقط خلال 24 ساعة من آخر رسالة من العميل.',
                ];
            }

            Log::error('WhatsApp Cloud send failed', [
                'phone' => $formattedPhone,
                'error' => $responseData,
            ]);

            return [
                'success' => false,
                'error' => $errorText ?? 'فشل إرسال الرسالة',
            ];
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
     * إرسال قالب Meta معتمد (لبدء المحادثة أو خارج نافذة 24 ساعة)
     *
     * @param  array<int, array<string, mixed>>  $components
     */
    public function sendTemplate(
        string $phoneNumber,
        string $templateName,
        string $languageCode = 'en_US',
        array $components = [],
        array $options = []
    ): array {
        try {
            $actorId = $options['user_id'] ?? auth()->id();
            $skipReadyCheck = (bool) ($options['skip_ready_check'] ?? false);
            $skipPacing = (bool) ($options['skip_pacing'] ?? false);
            $skipLog = (bool) ($options['skip_log'] ?? false);

            $formattedPhone = $this->formatPhoneNumber($phoneNumber);

            if (! $skipReadyCheck) {
                $ready = $this->cloud->canSendNow();
                if (! ($ready['success'] ?? false)) {
                    return ['success' => false, 'error' => $ready['error'] ?? 'الواتساب غير جاهز'];
                }
            }

            if (! $skipPacing && ($limitError = $this->pacing->assertCanSend())) {
                return ['success' => false, 'error' => $limitError];
            }

            if (! $skipPacing) {
                $this->pacing->waitBeforeSend($options['batch_id'] ?? null);
            }

            $creds = $this->cloud->resolveCredentials();
            $apiUrl = WhatsAppCloudSettings::apiUrl();
            $apiToken = $creds['access_token'];
            $phoneNumberId = $creds['phone_number_id'];

            if ($apiToken === '' || $phoneNumberId === '') {
                throw new \Exception('إعدادات WhatsApp Cloud API غير مكتملة');
            }

            $template = [
                'name' => $templateName,
                'language' => ['code' => $languageCode],
            ];

            if ($components !== []) {
                $template['components'] = $components;
            }

            $response = Http::withToken($apiToken)
                ->timeout(60)
                ->post("{$apiUrl}/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $formattedPhone,
                    'type' => 'template',
                    'template' => $template,
                ]);

            $responseData = $response->json() ?? [];
            $metaError = is_array($responseData['error'] ?? null) ? $responseData['error'] : null;
            $waMessageId = $responseData['messages'][0]['id'] ?? null;
            $accepted = $response->successful() && is_string($waMessageId) && $waMessageId !== '';

            $errorText = $accepted
                ? null
                : $this->cloud->humanizeSendError(
                    $metaError,
                    (string) ($metaError['message'] ?? 'فشل إرسال القالب')
                );

            if (! $skipLog) {
                $whatsappMessage = WhatsAppMessage::create([
                    'user_id' => $actorId,
                    'phone_number' => $formattedPhone,
                    'message' => '[قالب: ' . $templateName . ']',
                    'type' => 'template',
                    'status' => $accepted ? 'sent' : 'failed',
                    'response_data' => $responseData,
                    'whatsapp_message_id' => $waMessageId,
                    'sent_at' => $accepted ? now() : null,
                    'template_name' => $templateName,
                    'template_params' => ['language' => $languageCode, 'components' => $components],
                    'error_message' => $errorText,
                ]);

                if ($accepted) {
                    app(WhatsAppInboxService::class)->mirrorOutboundWhatsAppMessage($whatsappMessage);
                }
            }

            if ($accepted) {
                return [
                    'success' => true,
                    'accepted_by_meta' => true,
                    'whatsapp_id' => $waMessageId,
                    'phone' => $formattedPhone,
                ];
            }

            return ['success' => false, 'error' => $errorText ?? 'فشل إرسال القالب'];
        } catch (\Exception $e) {
            Log::error('WhatsApp template send error', [
                'phone' => $phoneNumber,
                'template' => $templateName,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => 'خطأ في إرسال القالب'];
        }
    }

    /**
     * إرسال رسالة نصية عبر Cloud API (للردود من صندوق الوارد)
     *
     * @return array{success: bool, whatsapp_id?: string, phone?: string, error?: string}
     */
    public function sendTextReply(string $phoneNumber, string $message, array $options = []): array
    {
        $options['skip_log'] = true;
        $options['skip_pacing'] = $options['skip_pacing'] ?? true;

        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            $skipReadyCheck = (bool) ($options['skip_ready_check'] ?? false);

            if (! $skipReadyCheck) {
                $ready = $this->cloud->canSendNow();
                if (! ($ready['success'] ?? false)) {
                    return ['success' => false, 'error' => $ready['error'] ?? 'الواتساب غير جاهز'];
                }
            }

            $creds = $this->cloud->resolveCredentials();
            $apiUrl = WhatsAppCloudSettings::apiUrl();
            $apiToken = $creds['access_token'];
            $phoneNumberId = $creds['phone_number_id'];

            if ($apiToken === '' || $phoneNumberId === '') {
                return ['success' => false, 'error' => 'إعدادات WhatsApp غير مكتملة'];
            }

            $response = Http::withToken($apiToken)
                ->timeout(60)
                ->post("{$apiUrl}/{$phoneNumberId}/messages", $this->buildTextMessagePayload($formattedPhone, $message, $options));

            $responseData = $response->json() ?? [];
            $metaError = is_array($responseData['error'] ?? null) ? $responseData['error'] : null;
            $waMessageId = $responseData['messages'][0]['id'] ?? null;
            $accepted = $response->successful() && is_string($waMessageId) && $waMessageId !== '';

            if ($accepted) {
                return [
                    'success' => true,
                    'whatsapp_id' => $waMessageId,
                    'phone' => $formattedPhone,
                ];
            }

            return [
                'success' => false,
                'error' => $this->cloud->humanizeSendError(
                    $metaError,
                    (string) ($metaError['message'] ?? 'فشل إرسال الرد')
                ),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'خطأ في إرسال الرد'];
        }
    }

    /**
     * تفاعل إيموجي على رسالة واردة (Meta Cloud API — reaction messages)
     *
     * @return array{success: bool, whatsapp_id?: string, error?: string}
     */
    public function sendReaction(string $phoneNumber, string $targetWaMessageId, string $emoji, array $options = []): array
    {
        $options['skip_log'] = true;
        $options['skip_pacing'] = $options['skip_pacing'] ?? true;

        try {
            $formattedPhone = $this->formatPhoneNumber($phoneNumber);
            $targetWaMessageId = trim($targetWaMessageId);
            $emoji = trim($emoji);

            if ($targetWaMessageId === '') {
                return ['success' => false, 'error' => 'معرّف الرسالة مطلوب للتفاعل'];
            }

            if (! (bool) ($options['skip_ready_check'] ?? false)) {
                $ready = $this->cloud->canSendNow();
                if (! ($ready['success'] ?? false)) {
                    return ['success' => false, 'error' => $ready['error'] ?? 'الواتساب غير جاهز'];
                }
            }

            $creds = $this->cloud->resolveCredentials();
            $apiUrl = WhatsAppCloudSettings::apiUrl();
            $apiToken = $creds['access_token'];
            $phoneNumberId = $creds['phone_number_id'];

            if ($apiToken === '' || $phoneNumberId === '') {
                return ['success' => false, 'error' => 'إعدادات WhatsApp غير مكتملة'];
            }

            $response = Http::withToken($apiToken)
                ->timeout(60)
                ->post("{$apiUrl}/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $formattedPhone,
                    'type' => 'reaction',
                    'reaction' => [
                        'message_id' => $targetWaMessageId,
                        'emoji' => $emoji,
                    ],
                ]);

            $responseData = $response->json() ?? [];
            $metaError = is_array($responseData['error'] ?? null) ? $responseData['error'] : null;
            $waMessageId = $responseData['messages'][0]['id'] ?? null;
            $accepted = $response->successful() && is_string($waMessageId) && $waMessageId !== '';

            if ($accepted) {
                return ['success' => true, 'whatsapp_id' => $waMessageId];
            }

            return [
                'success' => false,
                'error' => $this->cloud->humanizeSendError(
                    $metaError,
                    (string) ($metaError['message'] ?? 'فشل إرسال التفاعل')
                ),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'خطأ في إرسال التفاعل'];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildTextMessagePayload(string $to, string $body, array $options = []): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['body' => $body],
        ];

        $contextId = trim((string) ($options['context_message_id'] ?? ''));
        if ($contextId !== '') {
            $payload['context'] = ['message_id' => $contextId];
        }

        return $payload;
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
     * تنسيق رقم الهاتف (بدون +)
     */
    public function formatPhoneNumber(string $phoneNumber): string
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

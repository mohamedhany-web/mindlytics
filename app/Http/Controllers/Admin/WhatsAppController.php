<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\MessageTemplate;
use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppBatchService;
use App\Services\WhatsAppBridgeService;
use App\Services\WhatsAppPacingService;
use App\Services\WhatsAppService;
use App\Support\WhatsAppBridgeSettings;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function __construct(
        private WhatsAppBridgeService $bridge,
        private WhatsAppService $whatsapp,
        private WhatsAppBatchService $whatsappBatch
    ) {}

    public function index()
    {
        $settings = WhatsAppBridgeSettings::all();
        $statusResult = $this->bridge->getStatus();
        $status = $statusResult['success'] ? ($statusResult['data'] ?? []) : [];
        $bridgeError = $statusResult['success'] ? null : ($statusResult['error'] ?? null);
        $connectionMeta = $this->bridge->connectionMeta($status, $statusResult['success'] ?? false);

        $stats = [
            'total' => WhatsAppMessage::count(),
            'sent_today' => WhatsAppMessage::where('status', 'sent')->whereDate('created_at', today())->count(),
            'failed' => WhatsAppMessage::where('status', 'failed')->count(),
        ];

        $pacingStats = app(WhatsAppPacingService::class)->usageStats();

        return view('admin.whatsapp.index', compact('settings', 'status', 'bridgeError', 'connectionMeta', 'stats', 'pacingStats'));
    }

    public function sendForm()
    {
        $students = User::students()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->select('id', 'name', 'phone')
            ->orderBy('name')
            ->get();

        $templates = MessageTemplate::active()->get(['id', 'title', 'content', 'type']);
        $courses = AdvancedCourse::active()->select('id', 'title')->orderBy('title')->get();

        $statusResult = $this->bridge->getStatus();
        $bridgeStatus = $statusResult['success'] ? ($statusResult['data'] ?? []) : [];
        $connectionMeta = $this->bridge->connectionMeta($bridgeStatus, $statusResult['success'] ?? false);

        $recentMessages = WhatsAppMessage::with('user')
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.whatsapp.send', compact(
            'students',
            'templates',
            'courses',
            'bridgeStatus',
            'connectionMeta',
            'recentMessages'
        ));
    }

    public function sendMessage(Request $request)
    {
        $recipientType = $request->input('recipient_type', 'manual');

        if (in_array($recipientType, ['single_student', 'course_students', 'all_students'], true)) {
            return $this->sendBulkMessages($request);
        }

        $request->validate([
            'phone' => 'required|string|max:30',
            'message' => 'required|string|max:4096',
            'template_id' => 'nullable|exists:message_templates,id',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'message.required' => 'نص الرسالة مطلوب',
        ]);

        $message = $request->message;
        if ($request->filled('template_id')) {
            $template = MessageTemplate::findOrFail($request->template_id);
            $message = $template->render($this->genericTemplateVariables());
        }

        $result = $this->whatsapp->sendMessage($request->phone, $message);

        if ($result['success'] ?? false) {
            return redirect()
                ->route('admin.whatsapp.messages')
                ->with('success', 'تم إرسال الرسالة بنجاح.');
        }

        return back()
            ->withInput()
            ->with('error', $this->bridge->translateError($result['error'] ?? 'فشل إرسال الرسالة.'));
    }

    protected function sendBulkMessages(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:single_student,course_students,all_students',
            'user_id' => 'required_if:recipient_type,single_student|nullable|exists:users,id',
            'course_id' => 'required_if:recipient_type,course_students|nullable|exists:advanced_courses,id',
            'message' => 'required|string|max:4096',
            'template_id' => 'nullable|exists:message_templates,id',
        ], [
            'user_id.required_if' => 'يجب اختيار الطالب',
            'course_id.required_if' => 'يجب اختيار الكورس',
            'message.required' => 'نص الرسالة مطلوب',
        ]);

        $students = $this->resolveRecipients($request);

        if ($students->isEmpty()) {
            return back()->withInput()->with('error', 'لا يوجد مستلمون لديهم أرقام هواتف.');
        }

        $template = $request->filled('template_id')
            ? MessageTemplate::findOrFail($request->template_id)
            : null;

        $items = $students->shuffle()->map(function (User $student) use ($request, $template) {
            if (empty($student->phone)) {
                return null;
            }

            $finalMessage = $request->message;
            if ($template) {
                $finalMessage = $template->render($this->getStudentVariables($student));
            }

            return [
                'recipient_name' => $student->name,
                'phone' => $student->phone,
                'message' => $finalMessage,
                'message_type' => 'text',
                'user_id' => $student->id,
            ];
        })->filter();

        if ($items->isEmpty()) {
            return back()->withInput()->with('error', 'لا يوجد مستلمون لديهم أرقام هواتف.');
        }

        $batch = $this->whatsappBatch->createAndDispatch(
            sourceType: 'admin_bulk',
            sourceId: null,
            title: 'إرسال جماعي — ' . now()->format('Y-m-d H:i'),
            messageTemplate: $request->message,
            items: $items,
            createdBy: (int) auth()->id(),
            meta: [
                'recipient_type' => $request->recipient_type,
                'course_id' => $request->course_id,
                'template_id' => $request->template_id,
            ]
        );

        return redirect()
            ->route('admin.whatsapp.batches.show', $batch)
            ->with('success', 'تم بدء إرسال ' . $items->count() . ' رسالة في الخلفية.');
    }

    protected function resolveRecipients(Request $request)
    {
        return match ($request->recipient_type) {
            'single_student' => User::students()
                ->where('id', $request->user_id)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get(),
            'course_students' => User::students()
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->whereHas('courseEnrollments', fn ($q) => $q->where('advanced_course_id', $request->course_id))
                ->get(),
            'all_students' => User::students()
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get(),
            default => collect(),
        };
    }

    protected function getStudentVariables(User $student): array
    {
        $reportData = $this->whatsapp->generateStudentReportData($student);

        return [
            'student_name' => $student->name,
            'student_phone' => $student->phone,
            'courses_count' => count($reportData['courses']),
            'avg_score' => $reportData['overall']['average_score'],
            'total_exams' => count($reportData['exams']),
            'month_name' => now()->locale('ar')->format('F Y'),
            'overall_grade' => $reportData['overall']['grade'],
            'platform_name' => config('app.name', 'Mindlytics'),
            'support_phone' => config('services.platform.support_phone', ''),
            'date' => now()->format('d/m/Y'),
        ];
    }

    protected function genericTemplateVariables(): array
    {
        return [
            'student_name' => 'الطالب',
            'platform_name' => config('app.name', 'Mindlytics'),
            'date' => now()->format('d/m/Y'),
            'month_name' => now()->locale('ar')->format('F Y'),
        ];
    }

    public function messages(Request $request)
    {
        $messages = WhatsAppMessage::with('user')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('phone_number', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.whatsapp.messages', compact('messages'));
    }

    public function settings()
    {
        return view('admin.whatsapp.settings', [
            'settings' => WhatsAppBridgeSettings::all(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'service_type' => 'required|in:disabled,wwebjs,local,official,custom',
            'bridge_url' => 'nullable|url|max:500',
            'bridge_token' => 'nullable|string|max:500',
        ], [
            'bridge_url.url' => 'رابط الجسر غير صالح',
        ]);

        if (in_array($request->service_type, ['wwebjs', 'local'], true)) {
            $request->validate([
                'bridge_url' => 'required|url',
                'bridge_token' => 'required|string|min:8',
            ], [
                'bridge_url.required' => 'رابط سيرفر Node.js Bridge مطلوب',
                'bridge_token.required' => 'توكن الأمان مطلوب',
                'bridge_token.min' => 'التوكن يجب أن يكون 8 أحرف على الأقل',
            ]);
        }

        WhatsAppBridgeSettings::save([
            'service_type' => $request->service_type,
            'bridge_url' => $request->bridge_url ?? '',
            'bridge_token' => $request->bridge_token ?? '',
        ]);

        return back()->with('success', 'تم حفظ إعدادات الواتساب.');
    }

    public function statusJson()
    {
        return response()->json($this->bridge->getStatus());
    }

    public function qrJson()
    {
        return response()->json($this->bridge->getQr());
    }

    public function pairingCodeJson()
    {
        return response()->json($this->bridge->getPairingCode());
    }

    public function requestPairingCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:30',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب لرمز الربط',
        ]);

        $result = $this->bridge->requestPairingCode($request->phone);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success'] ?? false) {
            return back()->with('success', 'تم طلب رمز الربط — انتظر ظهور الرمز في اللوحة.');
        }

        return back()->with('error', $result['error'] ?? 'فشل طلب رمز الربط.');
    }

    public function switchToQrMode()
    {
        $result = $this->bridge->switchToQrMode();

        if (request()->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success'] ?? false) {
            return back()->with('success', 'تم التحويل لوضع QR.');
        }

        return back()->with('error', $result['error'] ?? 'فشل التحويل لوضع QR.');
    }

    public function docs()
    {
        $settings = WhatsAppBridgeSettings::all();
        $pacing = config('whatsapp.pacing', []);

        return view('admin.whatsapp.docs', compact('settings', 'pacing'));
    }

    public function startBridge()
    {
        $result = $this->bridge->start();

        if ($result['success'] ?? false) {
            $message = ! empty($result['restarting'])
                ? 'تم قتل Chrome العالق وإعادة تشغيل Bridge على VPS — انتظر 15 ثانية ثم حدّث الصفحة.'
                : 'تم إصلاح الاتصال — انتظر 10 ثوانٍ ثم حدّث الصفحة. الربط السابق محفوظ.';

            return back()->with('success', $message);
        }

        return back()->with('error', $result['error'] ?? 'فشل بدء الجسر.');
    }

    public function logoutBridge()
    {
        $result = $this->bridge->logout();

        if ($result['success'] ?? false) {
            return back()->with('success', 'تم قطع اتصال الواتساب.');
        }

        return back()->with('error', $result['error'] ?? 'فشل قطع الاتصال.');
    }
}

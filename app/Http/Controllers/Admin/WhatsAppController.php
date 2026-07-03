<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\MessageTemplate;
use App\Models\User;
use App\Models\WhatsAppBusinessConnection;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppMetaTemplate;
use App\Services\WhatsAppBatchService;
use App\Services\WhatsAppCloudService;
use App\Services\WhatsAppPacingService;
use App\Services\WhatsAppService;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class WhatsAppController extends Controller
{
    public function __construct(
        private WhatsAppCloudService $cloud,
        private WhatsAppService $whatsapp,
        private WhatsAppBatchService $whatsappBatch
    ) {}

    public function index()
    {
        $connectionMeta = $this->cloud->connectionMeta();
        $connection = WhatsAppBusinessConnection::active();

        $stats = [
            'total' => WhatsAppMessage::count(),
            'sent_today' => WhatsAppMessage::where('status', 'sent')->whereDate('created_at', today())->count(),
            'failed' => WhatsAppMessage::where('status', 'failed')->count(),
        ];

        $pacingStats = app(WhatsAppPacingService::class)->usageStats();

        return view('admin.whatsapp.index', compact('connectionMeta', 'connection', 'stats', 'pacingStats'));
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
        $connectionMeta = $this->cloud->connectionMeta();

        $recentMessages = WhatsAppMessage::with('user')
            ->latest()
            ->limit(6)
            ->get();

        $metaTemplates = $this->approvedMetaTemplatesForSend();
        $metaTemplatesError = null;
        if ($metaTemplates === [] && WhatsAppCloudSettings::isSendConfigured()) {
            $metaResult = $this->cloud->listApprovedTemplates();
            $metaTemplatesError = ($metaResult['success'] ?? false) ? null : ($metaResult['error'] ?? null);
        }

        return view('admin.whatsapp.send', compact(
            'students',
            'templates',
            'courses',
            'connectionMeta',
            'recentMessages',
            'metaTemplates',
            'metaTemplatesError',
        ));
    }

    public function sendMessage(Request $request)
    {
        $recipientType = $request->input('recipient_type', 'manual');
        $sendMode = $request->input('send_mode', 'text');

        if (in_array($recipientType, ['single_student', 'course_students', 'all_students'], true)) {
            if ($sendMode === 'meta_template') {
                if ($recipientType === 'single_student') {
                    return $this->sendSingleStudentMetaTemplate($request);
                }

                return back()
                    ->withInput()
                    ->with('error', 'إرسال قالب Meta الجماعي غير متاح بعد — استخدم «رقم يدوي» أو «طالب واحد».');
            }

            return $this->sendBulkMessages($request);
        }

        $request->validate([
            'send_mode' => 'required|in:text,meta_template',
            'phone' => 'required|string|max:30',
            'message' => ['nullable', 'string', 'max:4096', Rule::requiredIf($sendMode === 'text')],
            'template_id' => 'nullable|exists:message_templates,id',
            'meta_template_name' => ['nullable', 'string', 'max:200', Rule::requiredIf($sendMode === 'meta_template')],
            'meta_template_language' => ['nullable', 'string', 'max:20', Rule::requiredIf($sendMode === 'meta_template')],
            'template_variables' => 'nullable|array',
            'template_variables.*' => 'nullable|string|max:500',
        ], [
            'phone.required' => 'رقم الهاتف مطلوب',
            'message.required' => 'نص الرسالة مطلوب',
            'meta_template_name.required' => 'اختر قالب Meta معتمداً',
            'meta_template_language.required' => 'لغة القالب مطلوبة',
        ]);

        if ($sendMode === 'meta_template') {
            $components = $this->buildMetaTemplateComponents(
                (string) $request->input('meta_template_name'),
                (string) $request->input('meta_template_language'),
                $request->input('template_variables', [])
            );

            $result = $this->whatsapp->sendTemplate(
                $request->phone,
                (string) $request->input('meta_template_name'),
                (string) $request->input('meta_template_language'),
                $components,
                ['user_id' => auth()->id()]
            );
        } else {
            $message = (string) $request->message;
            if ($request->filled('template_id')) {
                $template = MessageTemplate::findOrFail($request->template_id);
                $message = $template->render($this->genericTemplateVariables());
            }

            $result = $this->whatsapp->sendMessage($request->phone, $message);
        }

        if ($result['success'] ?? false) {
            $flash = $result['notice'] ?? 'تم قبول الرسالة من Meta — تحقق من وصولها للمستلم في سجل الرسائل.';

            return redirect()
                ->route('admin.whatsapp.messages')
                ->with('success', $flash);
        }

        return back()
            ->withInput()
            ->with('error', $result['error'] ?? 'فشل إرسال الرسالة.');
    }

    protected function sendSingleStudentMetaTemplate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'meta_template_name' => 'required|string|max:200',
            'meta_template_language' => 'required|string|max:20',
            'template_variables' => 'nullable|array',
            'template_variables.*' => 'nullable|string|max:500',
        ], [
            'user_id.required' => 'يجب اختيار الطالب',
            'meta_template_name.required' => 'اختر قالب Meta معتمداً',
        ]);

        $student = User::students()
            ->where('id', $request->user_id)
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->first();

        if (! $student) {
            return back()->withInput()->with('error', 'الطالب المختار لا يملك رقم هاتف.');
        }

        $components = $this->buildMetaTemplateComponents(
            (string) $request->input('meta_template_name'),
            (string) $request->input('meta_template_language'),
            $request->input('template_variables', [])
        );

        $result = $this->whatsapp->sendTemplate(
            $student->phone,
            (string) $request->input('meta_template_name'),
            (string) $request->input('meta_template_language'),
            $components,
            ['user_id' => auth()->id()]
        );

        if ($result['success'] ?? false) {
            return redirect()
                ->route('admin.whatsapp.messages')
                ->with('success', $result['notice'] ?? 'تم إرسال قالب Meta للطالب.');
        }

        return back()->withInput()->with('error', $result['error'] ?? 'فشل الإرسال.');
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

        try {
            $this->cloud->assertReadyForBulkSend();
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

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

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function approvedMetaTemplatesForSend(): array
    {
        $templates = [];

        if (Schema::hasTable('whatsapp_meta_templates')) {
            $templates = WhatsAppMetaTemplate::query()
                ->where('status', WhatsAppMetaTemplate::STATUS_APPROVED)
                ->orderBy('name')
                ->get()
                ->map(fn (WhatsAppMetaTemplate $t) => [
                    'key' => $t->name . '|' . $t->language,
                    'name' => $t->name,
                    'language' => $t->language,
                    'category' => $t->category,
                    'label' => $t->displayLabel() . ' (' . $t->categoryLabel() . ')',
                    'body_text' => $t->body_text,
                    'body_variable_count' => (int) $t->body_variable_count,
                ])
                ->values()
                ->all();
        }

        if ($templates !== []) {
            return $templates;
        }

        $apiResult = $this->cloud->listApprovedTemplates();
        if (! ($apiResult['success'] ?? false)) {
            return [];
        }

        return collect($apiResult['templates'] ?? [])
            ->map(fn (array $row) => [
                'key' => ($row['name'] ?? '') . '|' . ($row['language'] ?? 'en_US'),
                'name' => $row['name'] ?? '',
                'language' => $row['language'] ?? 'en_US',
                'category' => $row['category'] ?? '',
                'label' => $row['label'] ?? (($row['name'] ?? '') . ' · ' . ($row['language'] ?? '')),
                'body_text' => null,
                'body_variable_count' => 0,
            ])
            ->filter(fn (array $row) => $row['name'] !== '')
            ->values()
            ->all();
    }

    /**
     * @param  array<int|string, string|null>  $variables
     * @return array<int, array<string, mixed>>
     */
    protected function buildMetaTemplateComponents(string $name, string $language, array $variables): array
    {
        $count = 0;
        if (Schema::hasTable('whatsapp_meta_templates')) {
            $tpl = WhatsAppMetaTemplate::query()
                ->where('name', $name)
                ->where('language', $language)
                ->where('status', WhatsAppMetaTemplate::STATUS_APPROVED)
                ->first();
            $count = (int) ($tpl?->body_variable_count ?? 0);
            if ($count === 0 && $tpl?->body_text) {
                $count = preg_match_all('/\{\{\d+\}\}/', (string) $tpl->body_text);
            }
        }

        if ($count < 1) {
            return [];
        }

        $parameters = [];
        for ($i = 1; $i <= $count; $i++) {
            $value = trim((string) ($variables[$i] ?? $variables[(string) $i] ?? ''));
            if ($value === '') {
                $value = '—';
            }
            $parameters[] = ['type' => 'text', 'text' => $value];
        }

        return [['type' => 'body', 'parameters' => $parameters]];
    }

    public function resendMessage(WhatsAppMessage $message)
    {
        if ($message->status !== 'failed') {
            return back()->with('error', 'يمكن إعادة إرسال الرسائل الفاشلة فقط.');
        }

        $result = $this->whatsapp->sendMessage(
            $message->phone_number,
            $message->message,
            $message->type,
            ['user_id' => $message->user_id]
        );

        if ($result['success'] ?? false) {
            $message->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);

            return back()->with('success', 'تم إعادة إرسال الرسالة بنجاح.');
        }

        return back()->with('error', $result['error'] ?? 'فشل إعادة الإرسال.');
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
            'config' => WhatsAppCloudSettings::formValues(),
            'connection' => WhatsAppBusinessConnection::active(),
            'connectionMeta' => $this->cloud->connectionMeta(),
        ]);
    }
}

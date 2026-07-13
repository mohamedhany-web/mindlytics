<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMetaTemplate;
use App\Services\ExcelWhatsAppCampaignService;
use App\Services\WhatsAppCloudService;
use App\Services\WhatsAppTemplateService;
use App\Support\WhatsAppCloudSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExcelWhatsAppController extends Controller
{
    public function __construct(
        private ExcelWhatsAppCampaignService $campaigns,
        private WhatsAppTemplateService $templates,
        private WhatsAppCloudService $cloud,
    ) {}

    public function index(): View
    {
        $approvedTemplates = WhatsAppMetaTemplate::query()
            ->where('status', WhatsAppMetaTemplate::STATUS_APPROVED)
            ->orderByDesc('updated_at')
            ->limit(80)
            ->get(['id', 'name', 'display_name', 'language', 'status', 'body_text', 'body_variable_count', 'category']);

        $recentDrafts = WhatsAppMetaTemplate::query()
            ->where('name', 'like', 'group_invite_%')
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get(['id', 'name', 'display_name', 'language', 'status', 'body_text', 'rejection_reason', 'updated_at']);

        return view('admin.whatsapp.excel-campaign', [
            'connectionMeta' => $this->cloud->connectionMeta(),
            'approvedTemplates' => $approvedTemplates,
            'recentDrafts' => $recentDrafts,
            'defaultBody' => $this->campaigns->defaultBody(),
            'variableLabels' => $this->campaigns->variableLabels(),
            'isOfficial' => WhatsAppCloudSettings::usesOfficial(),
        ]);
    }

    public function preview(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excel_file.required' => 'ارفع ملف Excel يحتوي على الأرقام.',
            'excel_file.mimes' => 'الصيغة المدعومة: xlsx أو xls أو csv.',
        ]);

        try {
            $parsed = $this->campaigns->parseRecipientsFromExcel($validated['excel_file']);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()
            ->withInput()
            ->with('excel_preview', [
                'valid' => $parsed['recipients']->take(30)->values()->all(),
                'valid_count' => $parsed['recipients']->count(),
                'skipped' => array_slice($parsed['skipped'], 0, 20),
                'skipped_count' => count($parsed['skipped']),
                'total_rows' => $parsed['total_rows'],
                'file_name' => $validated['excel_file']->getClientOriginalName(),
            ])
            ->with('success', 'تم ترتيب وتطبيع الأرقام: '.$parsed['recipients']->count().' صالح / '.count($parsed['skipped']).' متخطى.');
    }

    public function createTemplate(Request $request): RedirectResponse
    {
        if (! WhatsAppCloudSettings::usesOfficial()) {
            return back()->with('error', 'أكمل إعداد Meta Cloud API أولاً.');
        }

        $validated = $request->validate([
            'group_name' => 'required|string|max:120',
            'group_link' => 'required|string|max:500',
            'display_name' => 'nullable|string|max:255',
            'template_name' => 'nullable|string|max:512',
            'body_text' => 'nullable|string|max:1024',
            'footer_text' => 'nullable|string|max:60',
            'submit_now' => 'nullable|boolean',
        ], [
            'group_name.required' => 'اسم الجروب مطلوب.',
            'group_link.required' => 'لينك جروب واتساب مطلوب.',
        ]);

        $result = $this->campaigns->createAndSubmitInviteTemplate(
            array_merge($validated, [
                'submit_now' => $request->boolean('submit_now', true),
                'language' => 'ar_EG',
                'category' => 'UTILITY',
            ]),
            (int) auth()->id()
        );

        if (! ($result['success'] ?? false)) {
            return back()->withInput()->with('error', $result['error'] ?? 'فشل إنشاء القالب');
        }

        $template = $result['template'] ?? null;

        return back()
            ->withInput()
            ->with('success', $result['message'] ?? 'تم حفظ القالب.')
            ->with('created_template_id', $template?->id)
            ->with('created_template_name', $template?->name);
    }

    public function syncTemplates(): RedirectResponse
    {
        $result = $this->templates->syncFromMeta();
        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'فشلت مزامنة القوالب');
        }

        return back()->with('success', 'تمت مزامنة القوالب من Meta ('.($result['synced'] ?? 0).').');
    }

    public function send(Request $request): RedirectResponse
    {
        if (! WhatsAppCloudSettings::usesOfficial()) {
            return back()->with('error', 'أكمل إعداد Meta Cloud API أولاً.');
        }

        $validated = $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'group_name' => 'required|string|max:120',
            'group_link' => 'required|string|max:500',
            'template_id' => 'required|exists:whatsapp_meta_templates,id',
        ], [
            'excel_file.required' => 'ارفع ملف Excel بالأرقام.',
            'group_name.required' => 'اسم الجروب مطلوب.',
            'group_link.required' => 'لينك الجروب مطلوب.',
            'template_id.required' => 'اختر قالباً معتمداً للإرسال.',
        ]);

        $template = WhatsAppMetaTemplate::query()->findOrFail($validated['template_id']);

        try {
            $batch = $this->campaigns->dispatchInviteCampaign(
                $validated['excel_file'],
                $template,
                $validated['group_name'],
                $validated['group_link'],
                (int) auth()->id(),
                $validated['excel_file']->getClientOriginalName()
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.whatsapp.batches.show', $batch)
            ->with('success', 'تم بدء إرسال '.$batch->total_count.' دعوة — تابع التقدّم من دفعات الإرسال. الرسائل ستظهر في المحادثات بعد الإرسال.');
    }

    public function downloadSample()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setRightToLeft(true);
        $sheet->setTitle('أرقام');
        $sheet->fromArray([
            ['الاسم', 'الهاتف'],
            ['أحمد محمد', '01001234567'],
            ['سارة علي', '201098765432'],
            ['محمود حسن', '+201011122233'],
        ]);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'whatsapp-group-invite-sample.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}

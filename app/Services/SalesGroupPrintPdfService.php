<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Support\MpdfArabic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class SalesGroupPrintPdfService
{
    private const MAX_LEADS = 400;

    /**
     * طباعة نموذج ورقي لعملاء مجموعة — لكل الموظفين أو لموظف محدد.
     */
    public function download(SalesLeadGroup $group, ?User $employee = null): StreamedResponse
    {
        if (! class_exists(\Mpdf\Mpdf::class)) {
            abort(500, 'مكتبة PDF غير مثبتة على السيرفر. نفّذ: composer require mpdf/mpdf ثم composer install');
        }

        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $payload = $this->buildPayload($group, $employee);
        $filename = $this->filename($group, $employee);

        $binary = null;
        $lastError = null;

        try {
            $html = view('pdf.sales-group-print', $payload)->render();
            $binary = $this->renderHtmlToPdf($html, (string) $payload['doc_title'], true);
        } catch (Throwable $e) {
            $lastError = $e;
            Log::warning('Sales group print PDF landscape failed, trying portrait', [
                'group_id' => $group->id,
                'employee_id' => $employee?->id,
                'message' => $e->getMessage(),
            ]);
        }

        if ($binary === null || $binary === '') {
            try {
                $html = view('pdf.sales-group-print', $payload)->render();
                $binary = $this->renderHtmlToPdf($html, (string) $payload['doc_title'], false);
            } catch (Throwable $e) {
                $lastError = $e;
                Log::error('Sales group print PDF failed', [
                    'group_id' => $group->id,
                    'employee_id' => $employee?->id,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        if ($binary === null || $binary === '' || ! str_starts_with($binary, '%PDF')) {
            $hint = config('app.debug') && $lastError
                ? ' ('.$lastError->getMessage().')'
                : '';
            abort(500, 'تعذّر إنشاء ملف PDF للطباعة. جرّب تصفية بموظف واحد أو قلّل عدد العملاء.'.$hint);
        }

        return response()->streamDownload(
            function () use ($binary) {
                echo $binary;
            },
            $filename,
            [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
            ]
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function buildPayload(SalesLeadGroup $group, ?User $employee = null): array
    {
        $group->loadMissing(['members:id,name', 'assignee:id,name']);

        $leadsQuery = SalesLead::query()
            ->where('sales_lead_group_id', $group->id)
            ->with('assignee:id,name')
            ->orderBy('name');

        if ($employee) {
            $leadsQuery->where('assigned_to', $employee->id);
        }

        /** @var Collection<int, SalesLead> $allLeads */
        $allLeads = $leadsQuery->get();
        $total = $allLeads->count();
        $leads = $allLeads->take(self::MAX_LEADS)->values();

        if ($employee) {
            $sections = [[
                'employee' => $employee,
                'employee_name' => (string) ($employee->name ?: 'موظف'),
                'leads' => $leads,
            ]];
        } else {
            $sections = $leads
                ->groupBy(fn (SalesLead $l) => (int) ($l->assigned_to ?: 0))
                ->map(function (Collection $groupLeads) {
                    $first = $groupLeads->first();
                    $assignee = $first?->assignee;

                    return [
                        'employee' => $assignee,
                        'employee_name' => (string) ($assignee?->name ?: 'بدون إسناد'),
                        'leads' => $groupLeads->values(),
                    ];
                })
                ->sortBy(fn ($s) => $s['employee_name'] === 'بدون إسناد' ? 'zzz' : $s['employee_name'])
                ->values()
                ->all();
        }

        $employeeLabel = $employee?->name ?? 'كل الموظفين';
        $docTitle = 'نموذج متابعة مجموعة - '.$group->name.' - '.$employeeLabel;

        return [
            'group' => $group,
            'employee' => $employee,
            'employee_label' => $employeeLabel,
            'sections' => $sections,
            'leads_total' => $total,
            'leads_shown' => $leads->count(),
            'truncated' => $total > self::MAX_LEADS,
            'printed_at' => now(),
            'app_name' => (string) config('app.name', 'Mindlytics'),
            'doc_title' => $docTitle,
            'mode' => $employee ? 'employee' : 'all',
            'stage_labels' => SalesLead::STAGES,
            'priority_labels' => SalesLead::PRIORITIES,
        ];
    }

    private function filename(SalesLeadGroup $group, ?User $employee = null): string
    {
        $groupSlug = Str::slug($group->name, '-');
        if ($groupSlug === '') {
            $groupSlug = 'group-'.$group->id;
        }

        $date = now()->format('Ymd-His');

        if ($employee) {
            $empSlug = Str::slug((string) $employee->name, '-');
            if ($empSlug === '') {
                $empSlug = 'emp-'.$employee->id;
            }

            return sprintf('sales-group-%s-%s-%s.pdf', $groupSlug, $empSlug, $date);
        }

        return sprintf('sales-group-%s-all-%s.pdf', $groupSlug, $date);
    }

    private function renderHtmlToPdf(string $html, string $title, bool $landscape = true): string
    {
        $html = preg_replace(
            '/font-family\s*:\s*[^;]+;/i',
            'font-family: dejavusans, sans-serif;',
            $html
        ) ?? $html;

        $html = preg_replace('/<img\b[^>]*>/i', '', $html) ?? $html;

        // إزالة خصائص CSS قد تسبب أعطالاً على بعض بيئات الاستضافة
        $html = preg_replace('/border-radius\s*:\s*[^;]+;/i', '', $html) ?? $html;
        $html = preg_replace('/letter-spacing\s*:\s*[^;]+;/i', '', $html) ?? $html;

        $mpdf = MpdfArabic::make([
            'default_font' => 'dejavusans',
            'format' => $landscape ? 'A4-L' : 'A4',
            'orientation' => $landscape ? 'L' : 'P',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 14,
            'margin_header' => 4,
            'margin_footer' => 6,
        ]);

        $safeTitle = mb_substr(preg_replace('/[^\p{L}\p{N}\s\-_]+/u', ' ', $title) ?? $title, 0, 120);
        $mpdf->SetTitle($safeTitle !== '' ? $safeTitle : 'Sales Group Print');
        $mpdf->SetAuthor((string) config('app.name', 'Mindlytics'));

        $printed = e(now()->format('Y-m-d H:i'));
        $mpdf->SetHTMLFooter('
            <table width="100%" style="font-size:7pt;color:#64748b;border-top:1px solid #cbd5e1;">
                <tr>
                    <td width="40%" style="text-align:right;">Mindlytics - نموذج متابعة ميداني</td>
                    <td width="20%" style="text-align:center;">{PAGENO} / {nbpg}</td>
                    <td width="40%" style="text-align:left; direction:ltr;">'.$printed.'</td>
                </tr>
            </table>
        ');

        $mpdf->WriteHTML($html);

        return (string) $mpdf->Output('', 'S');
    }
}

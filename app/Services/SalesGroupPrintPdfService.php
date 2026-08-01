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

        try {
            $html = view('pdf.sales-group-print', $payload)->render();
            $binary = $this->renderHtmlToPdf($html, $payload['doc_title']);
        } catch (Throwable $e) {
            Log::error('Sales group print PDF failed', [
                'group_id' => $group->id,
                'employee_id' => $employee?->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $hint = config('app.debug') ? ' ('.$e->getMessage().')' : '';
            abort(500, 'تعذّر إنشاء ملف PDF للطباعة.'.$hint);
        }

        return response()->streamDownload(
            function () use ($binary) {
                echo $binary;
            },
            $filename,
            ['Content-Type' => 'application/pdf']
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

        /** @var Collection<int, SalesLead> $leads */
        $leads = $leadsQuery->get();

        // أقسام حسب الموظف — مفيدة لما تطبع الكل وتوزّع الورق
        if ($employee) {
            $sections = [[
                'employee' => $employee,
                'leads' => $leads,
            ]];
        } else {
            $sections = $leads
                ->groupBy(fn (SalesLead $l) => (int) ($l->assigned_to ?: 0))
                ->map(function (Collection $groupLeads, $assigneeId) {
                    $first = $groupLeads->first();

                    return [
                        'employee' => $first?->assignee,
                        'leads' => $groupLeads->values(),
                    ];
                })
                ->sortBy(fn ($s) => $s['employee']?->name ?? 'zzz')
                ->values()
                ->all();
        }

        $employeeLabel = $employee?->name ?? 'كل الموظفين';
        $docTitle = 'نموذج متابعة مجموعة — '.$group->name.' — '.$employeeLabel;

        return [
            'group' => $group,
            'employee' => $employee,
            'employee_label' => $employeeLabel,
            'sections' => $sections,
            'leads_total' => $leads->count(),
            'printed_at' => now(),
            'app_name' => config('app.name', 'Mindlytics'),
            'doc_title' => $docTitle,
            'mode' => $employee ? 'employee' : 'all',
        ];
    }

    private function filename(SalesLeadGroup $group, ?User $employee = null): string
    {
        $groupSlug = Str::slug($group->name, '-');
        if ($groupSlug === '') {
            $groupSlug = 'group-'.$group->id;
        }

        $date = now()->format('Ymd');

        if ($employee) {
            $empSlug = Str::slug($employee->name, '-');
            if ($empSlug === '') {
                $empSlug = 'emp-'.$employee->id;
            }

            // اسم واضح للتحميل: يحتوي اسم الموظف عند الإمكان + المعرّفات لضمان التفرد
            return sprintf('sales-group-%s-%s-%s.pdf', $groupSlug, $empSlug, $date);
        }

        return sprintf('sales-group-%s-all-%s.pdf', $groupSlug, $date);
    }

    private function renderHtmlToPdf(string $html, string $title): string
    {
        $html = preg_replace(
            '/font-family\s*:\s*[^;]+;/i',
            'font-family: dejavusans, sans-serif;',
            $html
        ) ?? $html;

        $html = preg_replace('/<img\b[^>]*>/i', '', $html) ?? $html;

        $mpdf = MpdfArabic::make([
            'default_font' => 'dejavusans',
            'format' => 'A4-L',
            'orientation' => 'L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 12,
        ]);
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor(config('app.name', 'Mindlytics'));
        $mpdf->SetHTMLFooter('
            <table width="100%" style="font-size:7.5pt;color:#64748b;border-top:1px solid #e2e8f0;padding-top:4px;">
                <tr>
                    <td width="33%" style="text-align:right;">Mindlytics Academy — نموذج متابعة ميداني</td>
                    <td width="34%" style="text-align:center;">صفحة {PAGENO} من {nbpg}</td>
                    <td width="33%" style="text-align:left;direction:ltr;">'.e(now()->format('Y-m-d H:i')).'</td>
                </tr>
            </table>
        ');
        $mpdf->WriteHTML($html);

        return (string) $mpdf->Output('', 'S');
    }
}

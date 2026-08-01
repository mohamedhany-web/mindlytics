<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Support\MpdfArabic;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class SalesGroupPrintPdfService
{
    private const MAX_LEADS = 300;

    /**
     * طباعة نموذج ورقي لعملاء مجموعة — لكل الموظفين أو لموظف محدد.
     *
     * @param  array{from?: int|null, to?: int|null}  $range  أرقام 1-based داخل القائمة المرتبة
     */
    public function download(SalesLeadGroup $group, ?User $employee = null, array $range = []): StreamedResponse
    {
        $this->ensureMpdfAvailable();

        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $payload = $this->buildPayload($group, $employee, $range);
        $filename = $this->filename($group, $employee, $payload);

        $binary = null;
        $errors = [];

        // 1) القالب الكامل — بنفس أسلوب تقرير المبيعات الشغّال على السيرفر
        try {
            $html = view('pdf.sales-group-print', $payload)->render();
            $binary = $this->renderLikeSalesReport($html, (string) $payload['doc_title']);
        } catch (Throwable $e) {
            $errors[] = 'full: '.$e->getMessage();
            Log::warning('Sales group print PDF full failed', [
                'group_id' => $group->id,
                'employee_id' => $employee?->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }

        // 2) HTML مبسّط جداً بدون CSS معقّد
        if ($binary === null || $binary === '' || ! $this->isPdf($binary)) {
            try {
                $html = $this->buildSimpleHtml($payload);
                $binary = $this->renderLikeSalesReport($html, (string) $payload['doc_title']);
            } catch (Throwable $e) {
                $errors[] = 'simple: '.$e->getMessage();
                Log::error('Sales group print PDF simple failed', [
                    'group_id' => $group->id,
                    'employee_id' => $employee?->id,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        }

        if ($binary === null || $binary === '' || ! $this->isPdf($binary)) {
            throw new RuntimeException(
                'تعذّر إنشاء ملف PDF. '.implode(' | ', $errors)
            );
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
     * @param  array{from?: int|null, to?: int|null}  $range
     * @return array<string, mixed>
     */
    public function buildPayload(SalesLeadGroup $group, ?User $employee = null, array $range = []): array
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
        $allLeads = $leadsQuery->get()->values();
        $totalAvailable = $allLeads->count();

        [$from, $to, $rangeLabel, $hasCustomRange] = $this->normalizeRange(
            $range['from'] ?? null,
            $range['to'] ?? null,
            $totalAvailable
        );

        // slice بفهارس 0-based مع الإبقاء على الترقيم الأصلي (من from)
        $leads = $totalAvailable > 0
            ? $allLeads->slice($from - 1, $to - $from + 1)->values()
            : collect();

        // حد أمان أقصى للطباعة في ملف واحد
        $hardCap = self::MAX_LEADS;
        $truncatedByCap = $leads->count() > $hardCap;
        if ($truncatedByCap) {
            $leads = $leads->take($hardCap)->values();
            $to = $from + $leads->count() - 1;
            $rangeLabel = $from.'-'.$to;
        }

        if ($employee) {
            $sections = [[
                'employee' => $employee,
                'employee_name' => (string) ($employee->name ?: 'موظف'),
                'leads' => $leads,
                'start_number' => $from,
            ]];
        } elseif ($hasCustomRange) {
            // نطاق محدد بدون موظف: قائمة واحدة متسلسلة بنفس ترتيب الأسماء
            $sections = [[
                'employee' => null,
                'employee_name' => 'كل الموظفين',
                'leads' => $leads,
                'start_number' => $from,
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
                        'start_number' => 1,
                    ];
                })
                ->sortBy(fn ($s) => $s['employee_name'] === 'بدون إسناد' ? 'zzz' : $s['employee_name'])
                ->values()
                ->all();
        }

        $employeeLabel = $employee?->name ?? 'كل الموظفين';
        $shown = $leads->count();

        return [
            'group' => $group,
            'employee' => $employee,
            'employee_label' => $employeeLabel,
            'sections' => $sections,
            'leads_total' => $totalAvailable,
            'leads_shown' => $shown,
            'range_from' => $shown > 0 ? $from : null,
            'range_to' => $shown > 0 ? ($from + $shown - 1) : null,
            'range_label' => $rangeLabel,
            'has_custom_range' => $hasCustomRange,
            'truncated' => $truncatedByCap,
            'printed_at' => now(),
            'app_name' => (string) config('app.name', 'Mindlytics'),
            'doc_title' => 'نموذج متابعة مجموعة - '.$group->name.' - '.$employeeLabel.($rangeLabel ? ' - '.$rangeLabel : ''),
            'mode' => $employee ? 'employee' : 'all',
            'stage_labels' => SalesLead::STAGES,
            'priority_labels' => SalesLead::PRIORITIES,
        ];
    }

    /**
     * @return array{0: int, 1: int, 2: string|null, 3: bool} [from, to, label, hasCustomRange]
     */
    private function normalizeRange(mixed $fromRaw, mixed $toRaw, int $total): array
    {
        if ($total <= 0) {
            return [1, 0, null, false];
        }

        $fromGiven = $fromRaw !== null && $fromRaw !== '';
        $toGiven = $toRaw !== null && $toRaw !== '';

        if (! $fromGiven && ! $toGiven) {
            return [1, $total, null, false];
        }

        $from = $fromGiven ? max(1, (int) $fromRaw) : 1;
        $to = $toGiven ? max(1, (int) $toRaw) : $total;

        if ($from > $total) {
            throw new RuntimeException("رقم البداية ({$from}) أكبر من عدد العملاء ({$total}).");
        }

        if ($to < $from) {
            throw new RuntimeException('رقم «إلى» يجب أن يكون أكبر من أو يساوي رقم «من».');
        }

        $to = min($to, $total);

        return [$from, $to, $from.'-'.$to, true];
    }

    /**
     * نفس مسار تقرير المبيعات الذي يعمل فعلياً على السيرفر.
     */
    private function renderLikeSalesReport(string $html, string $title): string
    {
        $html = preg_replace(
            '/font-family\s*:\s*[^;]+;/i',
            'font-family: dejavusans, sans-serif;',
            $html
        ) ?? $html;

        $html = preg_replace('/<img\b[^>]*>/i', '', $html) ?? $html;

        $mpdf = MpdfArabic::make(['default_font' => 'dejavusans']);
        $mpdf->SetTitle('نموذج مجموعة - '.mb_substr($title, 0, 80));
        $mpdf->SetAuthor((string) config('app.name', 'Mindlytics'));
        $mpdf->WriteHTML($html);

        return (string) $mpdf->Output('', 'S');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function buildSimpleHtml(array $payload): string
    {
        $group = $payload['group'];
        $stageLabels = $payload['stage_labels'] ?? [];
        $priorityLabels = $payload['priority_labels'] ?? [];
        $app = e((string) $payload['app_name']);
        $groupName = e((string) $group->name);
        $employeeLabel = e((string) $payload['employee_label']);
        $printed = e($payload['printed_at']->format('Y-m-d H:i'));
        $total = (int) $payload['leads_total'];
        $shown = (int) ($payload['leads_shown'] ?? $total);
        $rangeLabel = e((string) ($payload['range_label'] ?? ''));
        $rangeText = $rangeLabel !== ''
            ? ' | النطاق: '.$rangeLabel.' ('.$shown.' من '.$total.')'
            : ' | العدد: '.$shown;

        $rows = '';
        foreach ($payload['sections'] as $section) {
            $sectionName = e((string) ($section['employee_name'] ?? 'موظف'));
            $startNumber = max(1, (int) ($section['start_number'] ?? 1));
            $rows .= '<tr><td colspan="8" style="background:#0f766e;color:#fff;font-weight:bold;padding:6px;">ورقة عمل - '.$sectionName.'</td></tr>';

            foreach ($section['leads'] as $i => $lead) {
                $n = $startNumber + (int) $i;
                $stage = (string) ($lead->stage ?? '');
                $prio = (string) ($lead->priority ?: 'normal');
                $interest = trim(implode(' | ', array_filter([
                    trim((string) ($lead->interest ?? '')),
                    trim((string) ($lead->company ?? '')),
                ])));
                $notes = trim((string) ($lead->notes ?? ''));
                if (mb_strlen($notes) > 80) {
                    $notes = mb_substr($notes, 0, 80).'...';
                }

                $rows .= '<tr>'
                    .'<td style="border:1px solid #ccc;padding:4px;text-align:center;">'.$n.'</td>'
                    .'<td style="border:1px solid #ccc;padding:4px;">'.e((string) ($lead->name ?: '-')).'</td>'
                    .'<td style="border:1px solid #ccc;padding:4px;direction:ltr;text-align:left;">'.e((string) ($lead->phone ?: '-')).'</td>'
                    .'<td style="border:1px solid #ccc;padding:4px;">'.e($interest !== '' ? $interest : '-').'</td>'
                    .'<td style="border:1px solid #ccc;padding:4px;">'.e($stageLabels[$stage] ?? ($stage !== '' ? $stage : '-')).'</td>'
                    .'<td style="border:1px solid #ccc;padding:4px;">'.e($priorityLabels[$prio] ?? $prio).'</td>'
                    .'<td style="border:1px solid #ccc;padding:4px;">'.e($notes !== '' ? $notes : '-').'</td>'
                    .'<td style="border:1px solid #ccc;padding:4px;background:#fff7ed;">[&nbsp;] اتصال [&nbsp;] مهتم [&nbsp;] غير مهتم<br>[&nbsp;] متابعة [&nbsp;] تحويل<br>ملاحظات: ____________</td>'
                    .'</tr>';
            }
        }

        if ($rows === '') {
            $rows = '<tr><td colspan="8" style="padding:16px;text-align:center;border:1px solid #ccc;">لا يوجد عملاء للطباعة</td></tr>';
        }

        return '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">'
            .'<style>body{font-family:dejavusans,sans-serif;font-size:9pt;direction:rtl;color:#0f172a;} table{width:100%;border-collapse:collapse;} th{background:#134e4a;color:#fff;padding:5px;border:1px solid #0f766e;font-size:8pt;}</style>'
            .'</head><body>'
            .'<div style="border-bottom:3px solid #0f766e;padding-bottom:8px;margin-bottom:10px;">'
            .'<div style="font-size:14pt;font-weight:bold;color:#0f766e;">'.$app.'</div>'
            .'<div style="font-size:11pt;font-weight:bold;">نموذج متابعة ميداني - مجموعة: '.$groupName.'</div>'
            .'<div style="font-size:9pt;color:#475569;">الموظف: '.$employeeLabel.$rangeText.' | التاريخ: '.$printed.'</div>'
            .'</div>'
            .'<p style="font-size:8pt;background:#fffbeb;border:1px solid #fcd34d;padding:6px;">عبّئ نتيجة الاتصال يدوياً ثم سجّل الأكشن على النظام.</p>'
            .'<table><thead><tr>'
            .'<th>م</th><th>الاسم</th><th>الهاتف</th><th>الاهتمام</th><th>المرحلة</th><th>الأولوية</th><th>ملاحظات النظام</th><th>نتيجة الاتصال / ملاحظات</th>'
            .'</tr></thead><tbody>'.$rows.'</tbody></table>'
            .'<p style="font-size:8pt;color:#64748b;margin-top:10px;text-align:center;">Mindlytics - نموذج متابعة ميداني</p>'
            .'</body></html>';
    }

    private function isPdf(string $binary): bool
    {
        return str_starts_with(ltrim($binary), '%PDF');
    }

    private function ensureMpdfAvailable(): void
    {
        if (class_exists(\Mpdf\Mpdf::class, true)) {
            return;
        }

        $autoload = base_path('vendor/autoload.php');
        if (is_file($autoload)) {
            require_once $autoload;
        }

        if (class_exists(\Mpdf\Mpdf::class, true)) {
            return;
        }

        $mpdfSrc = base_path('vendor/mpdf/mpdf/src/Mpdf.php');
        if (is_file($mpdfSrc)) {
            throw new RuntimeException(
                'مكتبة mPDF موجودة لكن الـ autoload لا يحمّلها. على السيرفر نفّذ: composer dump-autoload -o'
            );
        }

        throw new RuntimeException(
            'مكتبة mPDF غير مثبتة في vendor. على السيرفر نفّذ: composer install --no-dev -o   أو: composer require mpdf/mpdf:^8.2'
        );
    }

    private function filename(SalesLeadGroup $group, ?User $employee = null, array $payload = []): string
    {
        $groupSlug = Str::slug($group->name, '-');
        if ($groupSlug === '') {
            $groupSlug = 'group-'.$group->id;
        }

        $date = now()->format('Ymd-His');
        $rangePart = ! empty($payload['range_label'])
            ? '-r'.preg_replace('/[^0-9\-]/', '', (string) $payload['range_label'])
            : '';

        if ($employee) {
            $empSlug = Str::slug((string) $employee->name, '-');
            if ($empSlug === '') {
                $empSlug = 'emp-'.$employee->id;
            }

            return sprintf('sales-group-%s-%s%s-%s.pdf', $groupSlug, $empSlug, $rangePart, $date);
        }

        return sprintf('sales-group-%s-all%s-%s.pdf', $groupSlug, $rangePart, $date);
    }
}

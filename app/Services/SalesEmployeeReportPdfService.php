<?php

namespace App\Services;

use App\Support\MpdfArabic;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class SalesEmployeeReportPdfService
{
    private const MAX_LEADS = 200;

    private const MAX_ACTIVITIES = 300;

    private const MAX_DAILY_ROWS = 93;

    /**
     * @param  array<string, mixed>  $report
     */
    public function download(array $report): StreamedResponse
    {
        if (! class_exists(\Mpdf\Mpdf::class)) {
            abort(500, 'مكتبة PDF غير مثبتة على السيرفر. نفّذ: composer require mpdf/mpdf ثم composer install');
        }

        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $rep = $report['rep'];
        $filename = sprintf(
            'sales-report-%s-%s-%s.pdf',
            (int) $rep->id,
            $report['start']->format('Y-m-d'),
            $report['end']->format('Y-m-d')
        );

        $pdfReport = $this->prepareForPdf($report);
        $binary = null;
        $lastError = null;

        try {
            $html = view('pdf.sales-employee-report', ['report' => $pdfReport])->render();
            $binary = $this->renderHtmlToPdf($html, (string) ($rep->name ?? ''));
        } catch (Throwable $e) {
            $lastError = $e;
            Log::warning('Sales report PDF full render failed, trying compact', [
                'employee_id' => $rep->id ?? null,
                'message' => $e->getMessage(),
            ]);
        }

        if ($binary === null || $binary === '') {
            try {
                $compact = $this->prepareForPdf($report, true);
                $html = view('pdf.sales-employee-report', ['report' => $compact])->render();
                $binary = $this->renderHtmlToPdf($html, (string) ($rep->name ?? ''));
            } catch (Throwable $e) {
                $lastError = $e;
                Log::error('Sales employee report PDF failed', [
                    'employee_id' => $rep->id ?? null,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        }

        if ($binary === null || $binary === '') {
            $hint = config('app.debug') && $lastError
                ? ' ('.$lastError->getMessage().')'
                : '';
            abort(500, 'تعذّر إنشاء ملف PDF حالياً. جرّب فترة أقصر أو تواصل مع الدعم.'.$hint);
        }

        return response()->streamDownload(
            function () use ($binary) {
                echo $binary;
            },
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    private function renderHtmlToPdf(string $html, string $title): string
    {
        // Force a safe Arabic-capable bundled font in CSS regardless of blade template.
        $html = preg_replace(
            '/font-family\s*:\s*[^;]+;/i',
            'font-family: dejavusans, sans-serif;',
            $html
        ) ?? $html;

        // Strip any images that might trip open_basedir / missing files on production.
        $html = preg_replace('/<img\b[^>]*>/i', '', $html) ?? $html;

        $mpdf = MpdfArabic::make(['default_font' => 'dejavusans']);
        $mpdf->SetTitle('تقرير مبيعات — '.$title);
        $mpdf->SetAuthor(config('app.name', 'Mindlytics'));
        $mpdf->WriteHTML($html);

        return (string) $mpdf->Output('', 'S');
    }

    /**
     * @param  array<string, mixed>  $report
     * @return array<string, mixed>
     */
    private function prepareForPdf(array $report, bool $compact = false): array
    {
        $maxLeads = $compact ? 50 : self::MAX_LEADS;
        $maxActivities = $compact ? 80 : self::MAX_ACTIVITIES;
        $maxDaily = $compact ? 62 : self::MAX_DAILY_ROWS;

        $leads = collect($report['leads_with_contact'] ?? $report['leads'] ?? []);
        $activities = collect($report['activities'] ?? []);
        $dailyRows = collect($report['daily_rows'] ?? []);

        $report['pdf_limits'] = [
            'leads_total' => $leads->count(),
            'leads_shown' => min($leads->count(), $maxLeads),
            'activities_total' => $activities->count(),
            'activities_shown' => min($activities->count(), $maxActivities),
            'daily_total' => $dailyRows->count(),
            'daily_shown' => min($dailyRows->count(), $maxDaily),
        ];

        $report['leads_with_contact'] = $leads->take($maxLeads)->values();
        $report['activities'] = $activities->take($maxActivities)->values();
        $report['daily_rows'] = $dailyRows->take($maxDaily)->values()->all();
        // Never embed logo paths on production (open_basedir / SVG / huge PNG often break mPDF).
        $report['pdf_logo_path'] = null;

        return $report;
    }
}

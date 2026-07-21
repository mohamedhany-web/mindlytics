<?php

namespace App\Services;

use App\Support\MpdfArabic;
use App\Support\SiteBranding;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class SalesEmployeeReportPdfService
{
    private const MAX_LEADS = 250;

    private const MAX_ACTIVITIES = 400;

    private const MAX_DAILY_ROWS = 120;

    /**
     * @param  array<string, mixed>  $report
     */
    public function download(array $report): StreamedResponse
    {
        if (! class_exists(\Mpdf\Mpdf::class)) {
            abort(500, 'مكتبة PDF غير مثبتة على السيرفر. نفّذ: composer require mpdf/mpdf ثم composer install');
        }

        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $rep = $report['rep'];
        $filename = sprintf(
            'sales-report-%s-%s-%s.pdf',
            (int) $rep->id,
            $report['start']->format('Y-m-d'),
            $report['end']->format('Y-m-d')
        );

        $pdfReport = $this->prepareForPdf($report);

        try {
            $html = view('pdf.sales-employee-report', ['report' => $pdfReport])->render();
            $mpdf = MpdfArabic::make();
            $mpdf->SetTitle('تقرير مبيعات — '.($rep->name ?? ''));
            $mpdf->SetAuthor(config('app.name', 'Mindlytics'));
            $mpdf->WriteHTML($html);
            $binary = $mpdf->Output('', 'S');
        } catch (Throwable $e) {
            Log::error('Sales employee report PDF failed', [
                'employee_id' => $rep->id ?? null,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            abort(500, 'تعذّر إنشاء ملف PDF حالياً. جرّب فترة أقصر أو تواصل مع الدعم.');
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
     * Trim oversized collections and attach a filesystem logo path for mPDF.
     *
     * @param  array<string, mixed>  $report
     * @return array<string, mixed>
     */
    private function prepareForPdf(array $report): array
    {
        $leads = collect($report['leads_with_contact'] ?? $report['leads'] ?? []);
        $activities = collect($report['activities'] ?? []);
        $dailyRows = collect($report['daily_rows'] ?? []);

        $report['pdf_limits'] = [
            'leads_total' => $leads->count(),
            'leads_shown' => min($leads->count(), self::MAX_LEADS),
            'activities_total' => $activities->count(),
            'activities_shown' => min($activities->count(), self::MAX_ACTIVITIES),
            'daily_total' => $dailyRows->count(),
            'daily_shown' => min($dailyRows->count(), self::MAX_DAILY_ROWS),
        ];

        $report['leads_with_contact'] = $leads->take(self::MAX_LEADS)->values();
        $report['activities'] = $activities->take(self::MAX_ACTIVITIES)->values();
        $report['daily_rows'] = $dailyRows->take(self::MAX_DAILY_ROWS)->values()->all();

        $logoPath = SiteBranding::logoLocalPath();
        $report['pdf_logo_path'] = ($logoPath && is_readable($logoPath) && ! str_ends_with(strtolower($logoPath), '.svg'))
            ? $logoPath
            : null;

        return $report;
    }
}

<?php

namespace App\Services;

use App\Support\MpdfArabic;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesEmployeeReportPdfService
{
    /**
     * @param  array<string, mixed>  $report
     */
    public function download(array $report): StreamedResponse
    {
        if (! class_exists(\Mpdf\Mpdf::class)) {
            abort(500, 'مكتبة PDF غير مثبتة على السيرفر. نفّذ: composer require mpdf/mpdf ثم composer install');
        }

        $rep = $report['rep'];
        $filename = sprintf(
            'sales-report-%s-%s-%s.pdf',
            (int) $rep->id,
            $report['start']->format('Y-m-d'),
            $report['end']->format('Y-m-d')
        );

        $html = view('pdf.sales-employee-report', compact('report'))->render();

        $mpdf = MpdfArabic::make();

        $mpdf->SetTitle('تقرير مبيعات — '.($rep->name ?? ''));
        $mpdf->SetAuthor(config('app.name', 'Mindlytics'));
        $mpdf->WriteHTML($html);

        $binary = $mpdf->Output('', 'S');

        return response()->streamDownload(
            static fn () => print $binary,
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }
}

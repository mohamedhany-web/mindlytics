<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesEmployeeReportPdfService
{
  /**
   * @param  array<string, mixed>  $report
   */
  public function download(array $report): StreamedResponse
  {
    if (! class_exists(\Dompdf\Dompdf::class)) {
      abort(500, 'مكتبة PDF غير مثبتة على السيرفر. نفّذ: composer require dompdf/dompdf ثم composer install');
    }

    $rep = $report['rep'];
    $filename = sprintf(
      'تقرير-مبيعات-%s-%s-%s.pdf',
      preg_replace('/\s+/', '-', (string) ($rep->name ?? 'موظف')),
      $report['start']->format('Y-m-d'),
      $report['end']->format('Y-m-d')
    );

    $html = view('pdf.sales-employee-report', compact('report'))->render();

    $dompdf = new \Dompdf\Dompdf([
      'isRemoteEnabled' => false,
      'isHtml5ParserEnabled' => true,
      'defaultFont' => 'DejaVu Sans',
    ]);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return response()->streamDownload(
      static fn () => print $dompdf->output(),
      $filename,
      ['Content-Type' => 'application/pdf']
    );
  }
}

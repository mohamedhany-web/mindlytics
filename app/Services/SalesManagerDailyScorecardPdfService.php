<?php

namespace App\Services;

use App\Models\SalesTeam;
use App\Support\MpdfArabic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class SalesManagerDailyScorecardPdfService
{
    /**
     * @param  array<string, mixed>  $board
     */
    public function downloadTeam(array $board): StreamedResponse
    {
        $date = $board['date'] instanceof Carbon ? $board['date'] : Carbon::parse($board['date']);
        $filename = 'sales-manager-scorecard-'.$date->format('Y-m-d').'.pdf';

        $binary = $this->render(view('pdf.sales-manager-daily-scorecard', [
            'board' => $board,
            'mode' => 'team',
            'row' => null,
        ])->render(), 'مركز الرقابة اليومية');

        return $this->stream($binary, $filename);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public function downloadEmployee(SalesTeam $team, Carbon $date, array $row): StreamedResponse
    {
        $filename = sprintf(
            'sales-scorecard-%d-%s.pdf',
            (int) $row['employee_id'],
            $date->format('Y-m-d')
        );

        $binary = $this->render(view('pdf.sales-manager-daily-scorecard', [
            'board' => [
                'date' => $date,
                'team' => $team,
                'rows' => [$row],
                'summary' => [
                    'members' => 1,
                    'avg_score' => $row['verified_score'],
                    'call_attempts' => $row['sos']['call_attempts_daily'] ?? 0,
                    'calls_answered' => $row['sos']['calls_answered_daily'] ?? 0,
                    'finance_verified_paid' => $row['financial']['finance_verified_paid'] ?? 0,
                    'exceptions_total' => count($row['exceptions']),
                ],
                'exceptions' => $row['exceptions'],
            ],
            'mode' => 'employee',
            'row' => $row,
        ])->render(), (string) ($row['name'] ?? 'موظف'));

        return $this->stream($binary, $filename);
    }

    private function render(string $html, string $title): string
    {
        if (! class_exists(\Mpdf\Mpdf::class)) {
            abort(500, 'مكتبة PDF غير مثبتة. نفّذ: composer require mpdf/mpdf');
        }

        try {
            $mpdf = MpdfArabic::make(['default_font' => 'dejavusans']);
            $mpdf->SetTitle(mb_substr($title, 0, 80));
            $mpdf->SetAuthor((string) config('app.name', 'Mindlytics'));
            $mpdf->WriteHTML($html);

            return (string) $mpdf->Output('', 'S');
        } catch (Throwable $e) {
            Log::error('Scorecard PDF failed', ['message' => $e->getMessage()]);
            abort(500, 'تعذّر إنشاء ملف PDF.');
        }
    }

    private function stream(string $binary, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($binary) {
            echo $binary;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }
}

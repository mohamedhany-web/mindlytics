<?php

namespace App\Domains\Marketing\Services;

use App\Domains\Sales\Models\SalesLead;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MetaCampaignLeadsExcelExportService
{
    private const HEADER_BG = '0F766E';

    private const TITLE_BG = '134E4A';

    private const ALT_ROW = 'F0FDFA';

    private const WHITE = 'FFFFFF';

    private const BORDER = '99F6E4';

    private const SLATE = '334155';

    /**
     * @param  Builder<SalesLead>  $query
     */
    public function buildSpreadsheet(
        Builder $query,
        string $campaignTitle,
        string $metaCampaignId,
        string $contextLabel = '',
    ): Spreadsheet {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('ليدز الحملة');
        $sheet->setRightToLeft(true);

        $headers = [
            '#',
            'الاسم',
            'الهاتف',
            'البريد',
            'المرحلة',
            'الأولوية',
            'مسند إلى',
            'نوع المصدر',
            'Meta Lead ID',
            'Ad ID',
            'دفعة الاستيراد',
            'ملاحظات',
            'تاريخ الإنشاء',
            'آخر تحديث',
        ];
        $lastColIndex = count($headers);
        $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);

        $total = (clone $query)->count();
        $formCount = (clone $query)->whereNotNull('meta_leadgen_id')->count();
        $inboxCount = max(0, $total - $formCount);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'تصدير ليدز حملة Meta Ads');
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => self::WHITE], 'name' => 'Arial'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::TITLE_BG]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(34);

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', $campaignTitle.'  ·  Campaign ID: '.$metaCampaignId);
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => self::WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::HEADER_BG]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue(
            'A3',
            sprintf(
                'الإجمالي: %d  |  Lead Forms: %d  |  محادثات/إعلانات: %d  |  تاريخ التصدير: %s%s',
                $total,
                $formCount,
                $inboxCount,
                now()->timezone(config('app.timezone'))->format('Y-m-d H:i'),
                $contextLabel !== '' ? '  |  '.$contextLabel : ''
            )
        );
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => self::SLATE]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(28);

        $headerRow = 5;
        foreach ($headers as $i => $header) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col.$headerRow, $header);
        }
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::HEADER_BG]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '115E59'],
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(30);

        $rowNum = $headerRow + 1;
        $n = 0;
        $alternate = false;

        foreach ($query->clone()->with('assignee')->orderByDesc('id')->cursor() as $lead) {
            /** @var SalesLead $lead */
            $n++;
            $sourceType = filled($lead->meta_leadgen_id) ? 'Lead Form' : 'محادثة إعلان';
            $notes = trim((string) ($lead->notes ?? ''));
            if (mb_strlen($notes) > 180) {
                $notes = mb_substr($notes, 0, 177).'…';
            }

            $values = [
                $n,
                $lead->name,
                $lead->phone,
                $lead->email,
                SalesLead::stageLabel($lead->stage),
                SalesLead::priorityLabel($lead->priority),
                $lead->assignee->name ?? '—',
                $sourceType,
                $lead->meta_leadgen_id ?: '—',
                $lead->meta_ad_id ?: '—',
                $lead->import_batch ?: '—',
                $notes !== '' ? $notes : '—',
                optional($lead->created_at)?->timezone(config('app.timezone'))->format('Y-m-d H:i'),
                optional($lead->updated_at)?->timezone(config('app.timezone'))->format('Y-m-d H:i'),
            ];

            foreach ($values as $i => $val) {
                $col = Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValueExplicit(
                    $col.$rowNum,
                    (string) ($val ?? ''),
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
            }

            $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")->applyFromArray([
                'font' => ['size' => 10, 'color' => ['rgb' => self::SLATE]],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $alternate ? self::ALT_ROW : self::WHITE],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => self::BORDER],
                    ],
                ],
            ]);
            $sheet->getStyle('A'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            $rowNum++;
            $alternate = ! $alternate;
        }

        if ($total === 0) {
            $sheet->mergeCells("A{$rowNum}:{$lastCol}{$rowNum}");
            $sheet->setCellValue("A{$rowNum}", 'لا توجد ليدز مسحوبة لهذه الحملة بعد.');
            $sheet->getStyle("A{$rowNum}")->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $rowNum++;
        }

        $sheet->setAutoFilter("A{$headerRow}:{$lastCol}{$headerRow}");
        $sheet->freezePane('A'.($headerRow + 1));

        $widths = [6, 22, 16, 26, 14, 12, 18, 14, 22, 18, 28, 36, 16, 16];
        foreach ($widths as $i => $width) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i + 1))->setWidth($width);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getHeaderFooter()->setOddFooter('&LMindlytics Meta Leads&Rصفحة &P / &N');

        return $spreadsheet;
    }

    public function writeToOutput(Spreadsheet $spreadsheet): void
    {
        (new Xlsx($spreadsheet))->save('php://output');
    }
}

<?php

namespace App\Services;

use App\Models\StudentCourseEnrollment;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OnlineEnrollmentsExcelExportService
{
    private const BLUE_950 = '0F172A';

    private const BLUE_800 = '1E40AF';

    private const BLUE_600 = '2563EB';

    private const BLUE_50 = 'EFF6FF';

    private const SLATE_600 = '475569';

    private const WHITE = 'FFFFFF';

    private const BORDER = 'BFDBFE';

    private const ALT_ROW = 'F8FAFC';

    /**
     * @return list<string>
     */
    public function headers(): array
    {
        return [
            '#',
            'اسم الطالب',
            'البريد الإلكتروني',
            'هاتف الطالب',
            'هاتف ولي الأمر',
            'الكورس',
            'السنة الدراسية',
            'المادة',
            'حالة التسجيل',
            'نسبة التقدم %',
            'نوع التسجيل',
            'طريقة الدفع',
            'السعر الأصلي (ج.م)',
            'الخصم (ج.م)',
            'السعر النهائي (ج.م)',
            'تفعيل مجاني / مخفي عن المدرب',
            'الملاحظات',
            'تاريخ التسجيل',
            'تاريخ التفعيل',
            'تم التفعيل بواسطة',
            'رقم الفاتورة',
            'رقم المدفوعة',
            'الفرع',
            'تاريخ الإنشاء',
            'آخر تحديث',
        ];
    }

    public function buildSpreadsheet(Builder $query, string $filterSummary = ''): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('تسجيلات الأونلاين');
        $sheet->setRightToLeft(true);

        $headers = $this->headers();
        $lastColIndex = count($headers);
        $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);

        $statsQuery = clone $query;
        $totalRows = (clone $statsQuery)->count();
        $activeCount = (clone $statsQuery)->where('status', 'active')->count();
        $pendingCount = (clone $statsQuery)->where('status', 'pending')->count();
        $sumFinal = (float) (clone $statsQuery)->sum('final_price');

        $logoPath = public_path('logo-removebg-preview.png');
        $titleStartCol = 'C';

        if ($logoPath && is_readable($logoPath)) {
            try {
                $drawing = new Drawing;
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setHeight(68);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(8);
                $drawing->setOffsetY(6);
                $drawing->setWorksheet($sheet);
                $sheet->mergeCells('A1:B4');
                $sheet->getColumnDimension('A')->setWidth(12);
                $sheet->getColumnDimension('B')->setWidth(4);
            } catch (\Throwable) {
                $titleStartCol = 'A';
            }
        } else {
            $titleStartCol = 'A';
        }

        if ($titleStartCol === 'A') {
            $sheet->mergeCells("A1:{$lastCol}1");
            $sheet->mergeCells("A2:{$lastCol}2");
            $sheet->mergeCells("A3:{$lastCol}3");
            $sheet->mergeCells("A4:{$lastCol}4");
            $titleRange = "A1:{$lastCol}1";
            $subRange = "A2:{$lastCol}2";
            $metaRange = "A3:{$lastCol}3";
            $statsRange = "A4:{$lastCol}4";
        } else {
            $sheet->mergeCells("{$titleStartCol}1:{$lastCol}1");
            $sheet->mergeCells("{$titleStartCol}2:{$lastCol}2");
            $sheet->mergeCells("{$titleStartCol}3:{$lastCol}3");
            $sheet->mergeCells("{$titleStartCol}4:{$lastCol}4");
            $titleRange = "{$titleStartCol}1:{$lastCol}1";
            $subRange = "{$titleStartCol}2:{$lastCol}2";
            $metaRange = "{$titleStartCol}3:{$lastCol}3";
            $statsRange = "{$titleStartCol}4:{$lastCol}4";
        }

        $tableHeaderRow = 6;
        $appName = config('app.name', 'Mindlytics');

        $sheet->setCellValue(Coordinate::stringFromColumnIndex(
            $titleStartCol === 'A' ? 1 : 3
        ).'1', 'تقرير تسجيلات الكورسات الأونلاين');
        $sheet->getStyle($titleRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => self::WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_800]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->setCellValue(Coordinate::stringFromColumnIndex(
            $titleStartCol === 'A' ? 1 : 3
        ).'2', $appName.' — بيانات التسجيل بالكامل');
        $sheet->getStyle($subRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::BLUE_950]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_50]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->setCellValue(Coordinate::stringFromColumnIndex(
            $titleStartCol === 'A' ? 1 : 3
        ).'3', 'تاريخ التصدير: '.now()->format('Y-m-d H:i').($filterSummary !== '' ? ' | '.$filterSummary : ''));
        $sheet->getStyle($metaRange)->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => self::SLATE_600]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->setCellValue(Coordinate::stringFromColumnIndex(
            $titleStartCol === 'A' ? 1 : 3
        ).'4', sprintf(
            'الإجمالي: %s | نشط: %s | انتظار: %s | مجموع الأسعار النهائية: %s ج.م',
            number_format($totalRows),
            number_format($activeCount),
            number_format($pendingCount),
            number_format($sumFinal, 2)
        ));
        $sheet->getStyle($statsRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::BLUE_800]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach ([1, 2, 3, 4] as $r) {
            $sheet->getRowDimension($r)->setRowHeight($r === 1 ? 30 : 22);
        }

        $c = 1;
        foreach ($headers as $header) {
            $col = Coordinate::stringFromColumnIndex($c);
            $sheet->setCellValue($col.$tableHeaderRow, $header);
            $c++;
        }

        $headerRange = 'A'.$tableHeaderRow.':'.$lastCol.$tableHeaderRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BLUE_600]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => self::BLUE_950],
                ],
            ],
        ]);
        $sheet->getRowDimension($tableHeaderRow)->setRowHeight(36);

        $priceCols = [
            Coordinate::stringFromColumnIndex(13),
            Coordinate::stringFromColumnIndex(14),
            Coordinate::stringFromColumnIndex(15),
        ];

        $rowNum = $tableHeaderRow + 1;
        $alternate = false;
        $serial = 1;

        $dataQuery = (clone $query)
            ->with([
                'student:id,name,email,phone,parent_phone',
                'course:id,title,academic_year_id,academic_subject_id',
                'course.academicYear:id,name',
                'course.academicSubject:id,name',
                'activatedBy:id,name',
                'branch:id,name',
            ])
            ->orderByDesc('enrolled_at')
            ->orderByDesc('id');

        foreach ($dataQuery->cursor() as $enrollment) {
            /** @var StudentCourseEnrollment $enrollment */
            $values = [
                $serial,
                $enrollment->student?->name ?? '—',
                $enrollment->student?->email ?? '—',
                $enrollment->student?->phone ?? '—',
                $enrollment->student?->parent_phone ?? '—',
                $enrollment->course?->title ?? '—',
                $enrollment->course?->academicYear?->name ?? '—',
                $enrollment->course?->academicSubject?->name ?? '—',
                $enrollment->status_text,
                $enrollment->progress !== null ? (float) $enrollment->progress : 0,
                $this->enrollmentTypeLabel($enrollment->enrollment_type),
                $this->paymentMethodLabel($enrollment->payment_method),
                $enrollment->original_price !== null ? (float) $enrollment->original_price : null,
                $enrollment->discount_amount !== null ? (float) $enrollment->discount_amount : null,
                $enrollment->final_price !== null ? (float) $enrollment->final_price : null,
                $enrollment->hide_from_instructor ? 'نعم' : 'لا',
                $enrollment->notes ?: '—',
                $enrollment->enrolled_at?->format('Y-m-d H:i') ?? '—',
                $enrollment->activated_at?->format('Y-m-d H:i') ?? '—',
                $enrollment->activatedBy?->name ?? '—',
                $enrollment->invoice_id ?: '—',
                $enrollment->payment_id ?: '—',
                $enrollment->branch?->name ?? '—',
                $enrollment->created_at?->format('Y-m-d H:i') ?? '—',
                $enrollment->updated_at?->format('Y-m-d H:i') ?? '—',
            ];

            $c = 1;
            foreach ($values as $val) {
                $col = Coordinate::stringFromColumnIndex($c);
                if (in_array($col, $priceCols, true) && $val === null) {
                    $sheet->setCellValue($col.$rowNum, '—');
                } else {
                    $sheet->setCellValue($col.$rowNum, $val ?? '');
                }
                $c++;
            }

            $range = 'A'.$rowNum.':'.$lastCol.$rowNum;
            $sheet->getStyle($range)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $alternate ? self::ALT_ROW : self::WHITE],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => self::BORDER],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);

            foreach ($priceCols as $priceCol) {
                $sheet->getStyle($priceCol.$rowNum)->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }

            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            $rowNum++;
            $serial++;
            $alternate = ! $alternate;
        }

        if ($totalRows === 0) {
            $sheet->setCellValue('A'.$rowNum, 'لا توجد تسجيلات مطابقة للتصفية الحالية.');
            $sheet->mergeCells('A'.$rowNum.':'.$lastCol.$rowNum);
            $sheet->getStyle('A'.$rowNum)->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => self::SLATE_600]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        $sheet->setAutoFilter('A'.$tableHeaderRow.':'.$lastCol.$tableHeaderRow);
        $sheet->freezePane('A'.($tableHeaderRow + 1));

        for ($i = 1; $i <= $lastColIndex; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getHeaderFooter()->setOddFooter('&L'.$appName.' — تسجيلات الأونلاين &Rصفحة &P / &N');

        return $spreadsheet;
    }

    public function streamDownload(Builder $query, string $filterSummary = '', ?string $filename = null): StreamedResponse
    {
        $spreadsheet = $this->buildSpreadsheet($query, $filterSummary);
        $filename = $filename ?: ('online-enrollments-'.now()->format('Y-m-d_His').'.xlsx');

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function paymentMethodLabel(?string $method): string
    {
        return match ($method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'online' => 'أونلاين',
            'wallet' => 'محفظة',
            'free' => 'مجاني',
            'other' => 'أخرى',
            default => $method ? (string) $method : '—',
        };
    }

    private function enrollmentTypeLabel(?string $type): string
    {
        return match ($type) {
            'purchase' => 'شراء',
            'gift' => 'هدية / مجاني',
            'scholarship' => 'منحة',
            default => $type ? (string) $type : '—',
        };
    }
}

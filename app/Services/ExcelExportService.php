<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelExportService
{
    protected $spreadsheet;
    protected $sheet;
    protected $currentRow = 1;
    
    // ألوان النظام (slate & blue)
    protected $colors = [
        'primary' => '3B82F6',      // blue-500
        'primary_dark' => '2563EB', // blue-600
        'primary_light' => '60A5FA', // blue-400
        'slate' => '475569',        // slate-600
        'slate_dark' => '1E293B',   // slate-800
        'slate_light' => '64748B',  // slate-500
        'header_bg' => '1E293B',    // slate-800
        'header_text' => 'FFFFFF',
        'title_bg' => '3B82F6',     // blue-500
        'title_text' => 'FFFFFF',
        'subtitle_bg' => 'F1F5F9',  // slate-100
        'subtitle_text' => '1E293B',
        'border' => 'CBD5E1',       // slate-300
        'alternate' => 'F8FAFC',    // slate-50
    ];

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }

    /**
     * إضافة الهيدر مع اللوجو
     */
    public function addHeader($title, $subtitle = null, $logoPath = null)
    {
        // إضافة اللوجو إذا كان موجوداً
        if ($logoPath && file_exists($logoPath)) {
            try {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Mindlytics Logo');
                $drawing->setPath($logoPath);
                $drawing->setHeight(60);
                $drawing->setWidth(60);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(10);
                $drawing->setWorksheet($this->sheet);
                
                // دمج الخلايا للوغو
                $this->sheet->mergeCells('A1:B3');
                $this->sheet->getRowDimension(1)->setRowHeight(35);
                $this->sheet->getRowDimension(2)->setRowHeight(25);
                $this->sheet->getRowDimension(3)->setRowHeight(20);
            } catch (\Exception $e) {
                // في حالة فشل إضافة اللوجو، تجاهل الخطأ
            }
        }

        // العنوان الرئيسي
        $titleCol = $logoPath ? 'C' : 'A';
        $this->sheet->setCellValue($titleCol . '1', $title);
        $this->sheet->mergeCells($titleCol . '1:H1');
        $this->sheet->getStyle($titleCol . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => $this->colors['title_text']],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $this->colors['title_bg']],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => $this->colors['border']],
                ],
            ],
        ]);
        $this->sheet->getRowDimension(1)->setRowHeight(35);

        // العنوان الفرعي
        if ($subtitle) {
            $this->sheet->setCellValue($titleCol . '2', $subtitle);
            $this->sheet->mergeCells($titleCol . '2:H2');
            $this->sheet->getStyle($titleCol . '2')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => $this->colors['subtitle_text']],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $this->colors['subtitle_bg']],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => $this->colors['border']],
                    ],
                ],
            ]);
            $this->sheet->getRowDimension(2)->setRowHeight(25);
            $this->currentRow = 3;
        } else {
            $this->currentRow = 2;
        }

        // معلومات إضافية
        $infoRow = $this->currentRow + 1;
        $this->sheet->setCellValue($titleCol . $infoRow, 'تاريخ التصدير: ' . now()->format('Y-m-d H:i:s'));
        $this->sheet->mergeCells($titleCol . $infoRow . ':H' . $infoRow);
        $this->sheet->getStyle($titleCol . $infoRow)->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => $this->colors['slate']],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $this->sheet->getRowDimension($infoRow)->setRowHeight(20);

        $this->currentRow = $infoRow + 2;
    }

    /**
     * إضافة عنوان قسم
     */
    public function addSectionTitle($title)
    {
        $this->sheet->setCellValue('A' . $this->currentRow, $title);
        $highestColumn = $this->sheet->getHighestColumn();
        $this->sheet->mergeCells('A' . $this->currentRow . ':' . $highestColumn . $this->currentRow);
        $this->sheet->getStyle('A' . $this->currentRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => $this->colors['header_text']],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $this->colors['header_bg']],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => $this->colors['primary']],
                ],
            ],
        ]);
        $this->sheet->getRowDimension($this->currentRow)->setRowHeight(28);
        $this->currentRow++;
    }

    /**
     * إضافة رأس الجدول
     */
    public function addTableHeader(array $headers, $startColumn = 'A')
    {
        $column = $startColumn;
        foreach ($headers as $header) {
            $this->sheet->setCellValue($column . $this->currentRow, $header);
            $this->sheet->getStyle($column . $this->currentRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => $this->colors['title_text']],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $this->colors['primary']],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => $this->colors['border']],
                    ],
                ],
            ]);
            $this->sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }
        $this->sheet->getRowDimension($this->currentRow)->setRowHeight(30);
        $this->currentRow++;
    }

    /**
     * إضافة صف بيانات
     */
    public function addTableRow(array $data, $startColumn = 'A', $isAlternate = false)
    {
        $column = $startColumn;
        foreach ($data as $cellData) {
            $this->sheet->setCellValue($column . $this->currentRow, $cellData);
            $bgColor = $isAlternate ? $this->colors['alternate'] : 'FFFFFF';
            
            $this->sheet->getStyle($column . $this->currentRow)->applyFromArray([
                'font' => [
                    'size' => 10,
                    'color' => ['rgb' => $this->colors['slate_dark']],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bgColor],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => $this->colors['border']],
                    ],
                ],
            ]);
            $column++;
        }
        $this->sheet->getRowDimension($this->currentRow)->setRowHeight(25);
        $this->currentRow++;
    }

    /**
     * إضافة بيانات الجدول
     */
    public function addTableData(array $headers, array $rows)
    {
        $this->addTableHeader($headers);
        
        $alternate = false;
        foreach ($rows as $row) {
            $this->addTableRow($row, 'A', $alternate);
            $alternate = !$alternate;
        }
    }

    /**
     * إضافة إحصائيات
     */
    public function addStatistics(array $stats)
    {
        $this->currentRow++;
        $startRow = $this->currentRow;
        
        foreach ($stats as $label => $value) {
            $this->sheet->setCellValue('A' . $this->currentRow, $label);
            $this->sheet->setCellValue('B' . $this->currentRow, $value);
            
            // تنسيق الخلايا
            $this->sheet->getStyle('A' . $this->currentRow . ':B' . $this->currentRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $this->colors['subtitle_bg']],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => $this->colors['border']],
                    ],
                ],
            ]);
            
            $this->sheet->getColumnDimension('A')->setWidth(30);
            $this->sheet->getColumnDimension('B')->setWidth(25);
            $this->sheet->getRowDimension($this->currentRow)->setRowHeight(25);
            $this->currentRow++;
        }
    }

    /**
     * إضافة صف فارغ
     */
    public function addEmptyRow($count = 1)
    {
        for ($i = 0; $i < $count; $i++) {
            $this->currentRow++;
        }
    }

    /**
     * حفظ وإرجاع الملف
     */
    public function download($filename)
    {
        // ضبط عرض الأعمدة تلقائياً
        $highestColumn = $this->sheet->getHighestColumn();
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $this->sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // حماية الورقة
        $this->sheet->getProtection()->setSheet(true);
        $this->sheet->getProtection()->setPassword('mindlytics');
        
        // إعداد Writer
        $writer = new Xlsx($this->spreadsheet);
        
        // إنشاء ملف مؤقت
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);
        
        // إرجاع الاستجابة
        return response()->download($tempFile, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * إرجاع Spreadsheet للاستخدام المتقدم
     */
    public function getSpreadsheet()
    {
        return $this->spreadsheet;
    }

    /**
     * إرجاع الورقة الحالية
     */
    public function getSheet()
    {
        return $this->sheet;
    }

    /**
     * الحصول على الصف الحالي
     */
    public function getCurrentRow()
    {
        return $this->currentRow;
    }

    /**
     * تعيين الصف الحالي
     */
    public function setCurrentRow($row)
    {
        $this->currentRow = $row;
    }

    /**
     * إضافة ورقة جديدة
     */
    public function addSheet($title)
    {
        $newSheet = $this->spreadsheet->createSheet();
        $newSheet->setTitle($title);
        $this->sheet = $newSheet;
        $this->currentRow = 1;
        return $this;
    }

    /**
     * الانتقال إلى ورقة معينة
     */
    public function setActiveSheet($index)
    {
        $this->spreadsheet->setActiveSheetIndex($index);
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->currentRow = 1;
        return $this;
    }
}

<?php

namespace App\Services;

use App\Models\StudentCourseEnrollment;
use App\Support\MpdfArabic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class OnlineEnrollmentsPdfExportService
{
    private const MAX_ROWS = 800;

    public function __construct(
        private CourseProgressService $progressService,
        private ScholarshipCurriculumVisibilityService $visibility,
    ) {}

    public function streamDownload(Builder $query, string $filterSummary = ''): StreamedResponse
    {
        $this->ensureMpdfAvailable();

        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $payload = $this->buildPayload($query, $filterSummary);
        $filename = 'online-enrollments-'.now()->format('Y-m-d_His').'.pdf';

        $binary = null;
        $errors = [];

        try {
            $html = view('pdf.online-enrollments-print', $payload)->render();
            $binary = $this->renderPdf($html, 'تسجيلات الأونلاين');
        } catch (Throwable $e) {
            $errors[] = 'full: '.$e->getMessage();
            Log::warning('Online enrollments PDF full failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }

        if ($binary === null || $binary === '' || ! $this->isPdf($binary)) {
            try {
                $binary = $this->renderPdf($this->buildSimpleHtml($payload), 'تسجيلات الأونلاين');
            } catch (Throwable $e) {
                $errors[] = 'simple: '.$e->getMessage();
                Log::error('Online enrollments PDF simple failed', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if ($binary === null || $binary === '' || ! $this->isPdf($binary)) {
            throw new RuntimeException('تعذّر إنشاء ملف PDF. '.implode(' | ', $errors));
        }

        return response()->streamDownload(
            function () use ($binary) {
                echo $binary;
            },
            $filename,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPayload(Builder $query, string $filterSummary): array
    {
        $statsBase = clone $query;
        $totalMatching = (clone $statsBase)->count();
        $activeCount = (clone $statsBase)->where('status', 'active')->count();
        $pendingCount = (clone $statsBase)->where('status', 'pending')->count();
        $finishedCount = (clone $statsBase)->finishedCurriculum()->count();
        $avgProgress = round((float) ((clone $statsBase)->avg('progress') ?? 0), 1);

        $rows = (clone $query)
            ->with(['student', 'course.academicYear', 'course.academicSubject', 'activatedBy'])
            ->latest('enrolled_at')
            ->limit(self::MAX_ROWS)
            ->get();

        $this->progressService->hydrateEnrollmentsWithLiveProgress($rows, $this->visibility, true);

        $printRows = $rows->values()->map(function (StudentCourseEnrollment $enrollment, int $index) {
            $pct = (float) ($enrollment->live_progress ?? $enrollment->progress ?? 0);
            $avgWatch = $enrollment->avg_lecture_watch_percent;
            $completed = $enrollment->live_completed;
            $total = $enrollment->live_total;

            return [
                'n' => $index + 1,
                'student_name' => (string) ($enrollment->student?->name ?? '—'),
                'phone' => (string) ($enrollment->student?->phone ?: '—'),
                'email' => (string) ($enrollment->student?->email ?: '—'),
                'course' => (string) ($enrollment->course?->title ?? '—'),
                'year_subject' => trim(implode(' — ', array_filter([
                    $enrollment->course?->academicYear?->name,
                    $enrollment->course?->academicSubject?->name,
                ]))) ?: '—',
                'status' => (string) ($enrollment->status_text ?? $enrollment->status ?? '—'),
                'progress' => number_format($pct, 1),
                'progress_raw' => $pct,
                'items' => ($completed !== null && $total !== null)
                    ? ((int) $completed).' / '.(int) $total
                    : '—',
                'avg_watch' => $avgWatch !== null ? number_format((float) $avgWatch, 0) : '—',
                'enrolled_at' => $enrollment->enrolled_at?->format('Y-m-d') ?? '—',
                'activated_at' => $enrollment->activated_at?->format('Y-m-d') ?? '—',
                'finished' => $pct >= 100.0,
            ];
        });

        return [
            'app_name' => (string) config('app.name', 'Mindlytics'),
            'doc_title' => 'تقرير تسجيلات الكورسات الأونلاين',
            'printed_at' => now(),
            'filter_summary' => $filterSummary !== '' ? $filterSummary : 'كل التسجيلات (بدون منحة)',
            'total_matching' => $totalMatching,
            'shown_count' => $printRows->count(),
            'truncated' => self::MAX_ROWS < $totalMatching,
            'active_count' => $activeCount,
            'pending_count' => $pendingCount,
            'finished_count' => $finishedCount,
            'avg_progress' => $avgProgress,
            'rows' => $printRows,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function buildSimpleHtml(array $payload): string
    {
        $app = e((string) $payload['app_name']);
        $printed = e($payload['printed_at']->format('Y-m-d H:i'));
        $filter = e((string) $payload['filter_summary']);
        $shown = (int) $payload['shown_count'];
        $total = (int) $payload['total_matching'];

        $trs = '';
        /** @var Collection $rows */
        $rows = $payload['rows'];
        foreach ($rows as $row) {
            $trs .= '<tr>'
                .'<td style="border:1px solid #cbd5e1;padding:5px;text-align:center;">'.(int) $row['n'].'</td>'
                .'<td style="border:1px solid #cbd5e1;padding:5px;">'.e($row['student_name']).'</td>'
                .'<td style="border:1px solid #cbd5e1;padding:5px;direction:ltr;text-align:left;">'.e($row['phone']).'</td>'
                .'<td style="border:1px solid #cbd5e1;padding:5px;">'.e($row['course']).'</td>'
                .'<td style="border:1px solid #cbd5e1;padding:5px;text-align:center;">'.e($row['status']).'</td>'
                .'<td style="border:1px solid #cbd5e1;padding:5px;text-align:center;font-weight:bold;">'.e($row['progress']).'%</td>'
                .'<td style="border:1px solid #cbd5e1;padding:5px;text-align:center;">'.e($row['items']).'</td>'
                .'<td style="border:1px solid #cbd5e1;padding:5px;text-align:center;">'.e($row['avg_watch']).'%</td>'
                .'</tr>';
        }

        return '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8">'
            .'<style>body{font-family:dejavusans,sans-serif;font-size:9pt;direction:rtl;color:#0f172a;} table{width:100%;border-collapse:collapse;} th{background:#1e40af;color:#fff;padding:6px;border:1px solid #1e3a8a;font-size:8pt;}</style>'
            .'</head><body>'
            .'<h1 style="font-size:14pt;margin:0 0 6px;">'.$app.' — تسجيلات الأونلاين</h1>'
            .'<p style="font-size:8pt;color:#475569;margin:0 0 10px;">طُبع: '.$printed.' | الفلتر: '.$filter.' | المعروض: '.$shown.' من '.$total.'</p>'
            .'<table><thead><tr>'
            .'<th>#</th><th>الطالب</th><th>الهاتف</th><th>الكورس</th><th>الحالة</th><th>التقدّم</th><th>العناصر</th><th>مشاهدة الفيديو</th>'
            .'</tr></thead><tbody>'.$trs.'</tbody></table>'
            .'</body></html>';
    }

    private function renderPdf(string $html, string $title): string
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
            'margin_top' => 12,
            'margin_bottom' => 12,
        ]);
        $mpdf->SetTitle(mb_substr($title, 0, 80));
        $mpdf->SetAuthor((string) config('app.name', 'Mindlytics'));
        $mpdf->SetHTMLFooter(
            '<div style="font-family:dejavusans,sans-serif;font-size:8pt;color:#64748b;text-align:center;direction:rtl;">'
            .'صفحة {PAGENO} من {nbpg} — '.e((string) config('app.name', 'Mindlytics'))
            .'</div>'
        );
        $mpdf->WriteHTML($html);

        return (string) $mpdf->Output('', 'S');
    }

    private function isPdf(string $binary): bool
    {
        return str_starts_with($binary, '%PDF');
    }

    private function ensureMpdfAvailable(): void
    {
        if (class_exists(\Mpdf\Mpdf::class)) {
            return;
        }

        if (is_dir(base_path('vendor/mpdf/mpdf'))) {
            throw new RuntimeException(
                'مكتبة mPDF موجودة لكن الـ autoload لا يحمّلها. على السيرفر نفّذ: composer dump-autoload -o'
            );
        }

        throw new RuntimeException(
            'مكتبة mPDF غير مثبتة في vendor. على السيرفر نفّذ: composer install --no-dev -o'
        );
    }
}

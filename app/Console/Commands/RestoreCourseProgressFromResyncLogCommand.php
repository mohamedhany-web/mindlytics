<?php

namespace App\Console\Commands;

use App\Models\StudentCourseEnrollment;
use Illuminate\Console\Command;

/**
 * يسترجع نسب progress المخزّنة قبل courses:resync-progress من لوج الأمر نفسه.
 * لا يمسّ lecture_watch_progress / lesson_progress (إنجاز المشاهدة الحقيقي).
 */
class RestoreCourseProgressFromResyncLogCommand extends Command
{
    protected $signature = 'courses:restore-progress-from-resync-log
                            {--dry-run : Show restores without writing}
                            {--builtin : Use the Aug 2026 production resync snapshot embedded in this command}';

    protected $description = 'Restore enrollment progress % values that were lowered by courses:resync-progress (from log snapshot)';

    public function handle(): int
    {
        if (! $this->option('builtin')) {
            $this->error('Pass --builtin to restore the known production snapshot from the Aug 2026 resync log.');
            $this->line('Example: php artisan courses:restore-progress-from-resync-log --builtin --dry-run');

            return self::FAILURE;
        }

        $rows = $this->builtinSnapshot();
        $dryRun = (bool) $this->option('dry-run');
        $restored = 0;
        $skipped = 0;
        $missing = 0;

        foreach ($rows as [$userId, $courseId, $oldProgress, $newProgress]) {
            $enrollment = StudentCourseEnrollment::query()
                ->where('user_id', $userId)
                ->where('advanced_course_id', $courseId)
                ->first();

            if (! $enrollment) {
                $missing++;
                $this->warn("missing enrollment user {$userId} course {$courseId}");
                continue;
            }

            $current = (float) $enrollment->progress;
            // استرجع فقط لو النسبة الحالية قريبة من القيمة الجديدة اللي كتبها الـ resync،
            // أو أقل من القيمة القديمة (عشان ما نكسرش لو طالب تقدّم بعد كده).
            $matchesNew = abs($current - $newProgress) < 0.05;
            $belowOld = $current + 0.05 < $oldProgress;

            if (! $matchesNew && ! $belowOld) {
                $skipped++;
                continue;
            }

            // لا نخفّض لو الطالب فعلاً أعلى من القديم
            $target = max($current, $oldProgress);
            if (abs($target - $current) < 0.001) {
                $skipped++;
                continue;
            }

            $this->line("user {$userId} course {$courseId}: {$current}% → {$target}% (restore)");
            if (! $dryRun) {
                $enrollment->update(['progress' => $target]);
            }
            $restored++;
        }

        $this->info("Done. restored={$restored} skipped={$skipped} missing={$missing}".($dryRun ? ' (dry-run)' : ''));

        return self::SUCCESS;
    }

    /**
     * Snapshot from production resync log (before → after).
     *
     * @return list<array{0:int,1:int,2:float,3:float}>
     */
    private function builtinSnapshot(): array
    {
        $lines = [
            '183 23 21.88 22.92',
            '198 23 16.67 1.04',
            '99 8 13.33 2.22',
            '324 8 12.5 0',
            '325 8 13.33 0',
            '209 8 68.89 22.22',
            '389 26 90.91 33.33',
            '387 26 63.64 21.74',
            '386 26 81.82 13.04',
            '384 26 87.5 18.75',
            '382 26 76.47 21.74',
            '381 26 76.47 4.35',
            '379 26 76.47 26.09',
            '365 26 81.25 39.13',
            '367 26 83.33 6.25',
            '368 26 100 62.5',
            '371 26 75 25',
            '245 26 75 4.35',
            '375 26 86.67 56.25',
            '374 26 83.33 8.33',
            '372 26 76.92 13.04',
            '331 26 83.33 21.74',
            '335 26 63.64 26.09',
            '337 26 0 4.35',
            '339 26 78.26 30.43',
            '340 26 76.47 26.09',
            '342 26 76.47 21.74',
            '344 26 82.35 17.39',
            '289 26 81.82 21.74',
            '361 26 90.91 8.7',
            '351 26 90.91 43.48',
            '358 26 76.47 26.09',
            '356 26 85.71 6.25',
            '355 26 75 17.39',
            '347 26 83.33 100',
            '332 26 100 36.36',
            '329 26 60 12.5',
            '395 26 81.82 21.74',
            '400 26 82.35 26.09',
            '399 26 0 4.35',
            '396 26 76.47 8.7',
            '89 8 60 2.22',
            '403 8 22.22 6.67',
            '404 26 75 26.09',
            '405 26 76.92 8.7',
            '409 8 22.22 8.89',
            '309 8 66.67 2.22',
            '424 8 13.33 0',
            '413 26 0 4.35',
            '191 8 22.22 0',
            '435 8 15.56 0',
            '326 8 13.33 2.22',
            '450 8 20 0',
            '441 8 20 0',
            '452 8 66.67 15.56',
            '451 8 22.22 6.67',
            '251 8 35.56 8.89',
            '455 8 20 0',
            '457 8 35.56 6.67',
            '458 8 66.67 68.89',
            '460 8 68.89 17.78',
            '461 8 35.56 6.67',
            '463 8 68.89 15.56',
            '466 8 22.22 6.67',
            '467 8 22.22 2.22',
            '469 8 33.33 42.22',
            '471 8 22.22 4.44',
            '476 8 68.89 0',
            '478 8 68.89 31.11',
            '479 8 68.89 0',
            '480 8 60 6.67',
            '481 8 68.89 6.67',
            '484 8 60 4.44',
            '486 8 60 4.44',
            '487 8 60 0',
            '490 8 60 2.22',
            '54 8 60 0',
            '491 8 60 0',
            '462 8 60 0',
        ];

        return array_map(function (string $line) {
            [$userId, $courseId, $old, $new] = preg_split('/\s+/', trim($line));

            return [(int) $userId, (int) $courseId, (float) $old, (float) $new];
        }, $lines);
    }
}

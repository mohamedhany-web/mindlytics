<?php

namespace App\Console\Commands;

use App\Models\AdvancedCourse;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\CourseProgressService;
use App\Services\ScholarshipCurriculumVisibilityService;
use Illuminate\Console\Command;

class ResyncCourseProgressCommand extends Command
{
    protected $signature = 'courses:resync-progress
                            {--course= : Limit to a specific advanced_course_id}
                            {--user= : Limit to a specific user_id}
                            {--dry-run : Show changes without writing}';

    protected $description = 'Recalculate stored student course progress from curriculum completion (fixes stuck progress bars)';

    public function handle(
        CourseProgressService $progressService,
        ScholarshipCurriculumVisibilityService $visibility,
    ): int {
        $query = StudentCourseEnrollment::query()
            ->whereIn('status', ['active', 'completed']);

        if ($courseId = $this->option('course')) {
            $query->where('advanced_course_id', (int) $courseId);
        }
        if ($userId = $this->option('user')) {
            $query->where('user_id', (int) $userId);
        }

        $total = (clone $query)->count();
        $this->info("Enrollments to process: {$total}");
        if ($total === 0) {
            return self::SUCCESS;
        }

        $updated = 0;
        $unchanged = 0;
        $failed = 0;
        $dryRun = (bool) $this->option('dry-run');
        $courseCache = [];

        $query->orderBy('id')->chunkById(100, function ($rows) use (
            $progressService,
            $visibility,
            $dryRun,
            &$updated,
            &$unchanged,
            &$failed,
            &$courseCache
        ) {
            foreach ($rows as $enrollment) {
                try {
                    $courseId = (int) $enrollment->advanced_course_id;
                    if (! isset($courseCache[$courseId])) {
                        $courseCache[$courseId] = AdvancedCourse::query()->find($courseId);
                    }
                    $course = $courseCache[$courseId];
                    $user = User::query()->find($enrollment->user_id);
                    if (! $course || ! $user) {
                        $failed++;
                        continue;
                    }

                    $sections = $course->activeSections()
                        ->with([
                            'visibleStudents:id',
                            'visibleGroups.members:id',
                            'activeItems' => fn ($q) => $q->with(['item', 'visibleStudents:id', 'visibleGroups.members:id']),
                        ])
                        ->orderBy('order')
                        ->get();
                    $sections = $visibility->filterSectionsForStudent($sections, $user, $course);
                    $after = $progressService->getCourseProgress($user, $course, $sections);
                    $before = (float) $enrollment->progress;

                    if (abs($after - $before) > 0.001) {
                        $updated++;
                        $this->line("user {$enrollment->user_id} course {$courseId}: {$before}% → {$after}%");
                        if (! $dryRun) {
                            $progressService->syncEnrollmentProgress((int) $user->id, $courseId, $after);
                        }
                    } else {
                        $unchanged++;
                    }
                } catch (\Throwable $e) {
                    $failed++;
                    $this->error("Failed enrollment {$enrollment->id}: {$e->getMessage()}");
                }
            }
        });

        $this->info("Done. updated={$updated} unchanged={$unchanged} failed={$failed}".($dryRun ? ' (dry-run)' : ''));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\AdvancedExam;
use App\Models\Assignment;
use App\Models\CourseLesson;
use App\Models\Lecture;
use App\Models\LectureVideoQuestionAnswer;
use App\Models\LectureWatchProgress;
use App\Models\LearningPattern;
use App\Models\LessonProgress;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Support\Collection;

class CourseProgressService
{
    /**
     * تحميل علاقات التقدم للمستخدم الحالي على عناصر المنهج.
     */
    public function loadCurriculumProgressForUser(Collection $sections, User $user): void
    {
        foreach ($sections as $section) {
            $items = $section->relationLoaded('activeItems')
                ? $section->activeItems
                : ($section->items ?? collect());

            foreach ($items as $curriculumItem) {
                $entity = $curriculumItem->item ?? null;
                if (! $entity) {
                    continue;
                }

                if ($entity instanceof CourseLesson) {
                    $entity->load(['progress' => fn ($q) => $q->where('user_id', $user->id)]);
                } elseif ($entity instanceof Lecture) {
                    $entity->load(['watchProgress' => fn ($q) => $q->where('user_id', $user->id)]);
                } elseif ($entity instanceof LearningPattern) {
                    $entity->load(['attempts' => fn ($q) => $q->where('user_id', $user->id)->latest()]);
                } elseif ($entity instanceof AdvancedExam) {
                    $entity->load(['attempts' => fn ($q) => $q->where('user_id', $user->id)->whereNotNull('submitted_at')]);
                } elseif ($entity instanceof Assignment) {
                    $entity->load(['submissions' => fn ($q) => $q->where('student_id', $user->id)]);
                }
            }
        }
    }

    /**
     * هل العنصر مكتمل لهذا الطالب؟
     */
    public function isItemCompletedForUser(mixed $entity, User $user): bool
    {
        if ($entity instanceof CourseLesson) {
            $p = $this->lessonProgressForUser($entity, $user);

            return $p && $p->is_completed;
        }

        if ($entity instanceof Lecture) {
            return $this->isLectureCompletedForUser($entity, $user);
        }

        if ($entity instanceof Assignment) {
            if ($entity->relationLoaded('submissions')) {
                return $entity->submissions->where('student_id', $user->id)->isNotEmpty();
            }

            return $entity->submissions()->where('student_id', $user->id)->exists();
        }

        if ($entity instanceof LearningPattern) {
            $best = $entity->getUserBestAttempt($user->id);

            return $best && $best->status === 'completed';
        }

        if ($entity instanceof AdvancedExam) {
            $passing = (float) ($entity->passing_marks ?? 0);
            if ($entity->relationLoaded('attempts')) {
                return $entity->attempts->contains(
                    fn ($a) => $a->score !== null && (float) $a->score >= $passing
                );
            }

            return $entity->attempts()
                ->where('user_id', $user->id)
                ->whereNotNull('submitted_at')
                ->whereNotNull('score')
                ->where('score', '>=', $passing)
                ->exists();
        }

        return false;
    }

    public function isLectureCompletedForUser(Lecture $lecture, User $user): bool
    {
        $wp = $this->watchProgressForUser($lecture, $user);
        $threshold = $this->lectureCompletionThreshold($lecture);

        if ($wp && ((bool) $wp->is_completed || (int) $wp->progress_percent >= $threshold)) {
            return true;
        }

        $vqIds = $lecture->videoQuestions()->pluck('id');
        if ($vqIds->isEmpty()) {
            return false;
        }

        $answered = LectureVideoQuestionAnswer::query()
            ->where('user_id', $user->id)
            ->whereIn('lecture_video_question_id', $vqIds)
            ->distinct('lecture_video_question_id')
            ->count('lecture_video_question_id');

        return $answered >= $vqIds->count();
    }

    public function lectureCompletionThreshold(Lecture $lecture): int
    {
        $minPercent = $lecture->min_watch_percent_to_unlock_next;

        return $minPercent !== null ? (int) $minPercent : 90;
    }

    /**
     * @return array{0: float, 1: int, 2: int} [progress%, total, completed]
     */
    public function calculateFromSections(User $user, AdvancedCourse $course, Collection $sections): array
    {
        if ($sections->isEmpty()) {
            $lessonIds = $course->lessons()->where('is_active', true)->pluck('id');
            $total = $lessonIds->count();
            $completed = LessonProgress::query()
                ->where('user_id', $user->id)
                ->whereIn('course_lesson_id', $lessonIds)
                ->where('is_completed', true)
                ->count();
            $progress = $total > 0 ? round(($completed / $total) * 100, 2) : 0.0;

            return [$progress, $total, $completed];
        }

        $totalItems = 0;
        $completedItems = 0;

        foreach ($sections as $section) {
            $items = $section->relationLoaded('activeItems')
                ? $section->activeItems
                : ($section->items ?? collect());

            foreach ($items as $item) {
                $entity = $item->item ?? null;
                if (! $entity) {
                    continue;
                }

                $totalItems++;
                if ($this->isItemCompletedForUser($entity, $user)) {
                    $completedItems++;
                }
            }
        }

        $progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0.0;

        return [$progress, $totalItems, $completedItems];
    }

    public function getCourseProgress(User $user, AdvancedCourse $course, Collection $sections): float
    {
        $this->loadCurriculumProgressForUser($sections, $user);
        [$progress] = $this->calculateFromSections($user, $course, $sections);

        return (float) $progress;
    }

    public function syncEnrollmentProgress(int $userId, int $courseId, float $progress): void
    {
        // لا نغيّر status إلى completed هنا — activeCourses يعتمد على status=active.
        StudentCourseEnrollment::query()
            ->where('user_id', $userId)
            ->where('advanced_course_id', $courseId)
            ->update(['progress' => $progress]);
    }

    private function watchProgressForUser(Lecture $lecture, User $user): ?LectureWatchProgress
    {
        if ($lecture->relationLoaded('watchProgress')) {
            return $lecture->watchProgress->firstWhere('user_id', $user->id);
        }

        return LectureWatchProgress::query()
            ->where('lecture_id', $lecture->id)
            ->where('user_id', $user->id)
            ->first();
    }

    private function lessonProgressForUser(CourseLesson $lesson, User $user): ?LessonProgress
    {
        if ($lesson->relationLoaded('progress')) {
            return $lesson->progress->firstWhere('user_id', $user->id);
        }

        return LessonProgress::query()
            ->where('course_lesson_id', $lesson->id)
            ->where('user_id', $user->id)
            ->first();
    }
}

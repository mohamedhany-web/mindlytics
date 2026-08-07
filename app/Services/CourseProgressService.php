<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\AdvancedExam;
use App\Models\Assignment;
use App\Models\CourseLesson;
use App\Models\Exam;
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
                } elseif ($entity instanceof AdvancedExam || $entity instanceof Exam) {
                    $entity->load(['attempts' => fn ($q) => $q->where('user_id', $user->id)->whereNotNull('submitted_at')]);
                } elseif ($entity instanceof Assignment) {
                    $entity->load(['submissions' => fn ($q) => $q->where('student_id', $user->id)]);
                }
            }
        }
    }

    public function isRecognizedCurriculumEntity(mixed $entity): bool
    {
        return $entity instanceof CourseLesson
            || $entity instanceof Lecture
            || $entity instanceof Assignment
            || $entity instanceof LearningPattern
            || $entity instanceof AdvancedExam
            || $entity instanceof Exam;
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

        if ($entity instanceof AdvancedExam || $entity instanceof Exam) {
            return $this->isExamPassedForUser($entity, $user);
        }

        return false;
    }

    /**
     * هل نجح الطالب في الامتحان (AdvancedExam و Exam نفس جدول exams)؟
     */
    public function isExamPassedForUser(AdvancedExam|Exam $exam, User $user): bool
    {
        $passing = (float) ($exam->passing_marks ?? 0);
        if ($exam->relationLoaded('attempts')) {
            return $exam->attempts->contains(
                fn ($a) => $a->score !== null && (float) $a->score >= $passing
            );
        }

        return $exam->attempts()
            ->where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->whereNotNull('score')
            ->where('score', '>=', $passing)
            ->exists();
    }

    /**
     * اكتمال المحاضرة: مشاهدة كافية (+ كل أسئلة الفيديو إن وُجدت).
     * لا نعتبر الإجابة على الأسئلة وحدها إكمالاً بدون مشاهدة — مهم للشهادات.
     */
    public function isLectureCompletedForUser(Lecture $lecture, User $user): bool
    {
        $wp = $this->watchProgressForUser($lecture, $user);
        $threshold = $this->lectureCompletionThreshold($lecture);
        $watchedEnough = $wp && ((bool) $wp->is_completed || (int) $wp->progress_percent >= $threshold);

        if (! $watchedEnough) {
            return false;
        }

        $vqIds = $lecture->videoQuestions()->pluck('id');
        if ($vqIds->isEmpty()) {
            return true;
        }

        $answered = LectureVideoQuestionAnswer::query()
            ->where('user_id', $user->id)
            ->whereIn('lecture_video_question_id', $vqIds)
            ->distinct()
            ->count('lecture_video_question_id');

        return $answered >= $vqIds->count();
    }

    public function lectureCompletionThreshold(Lecture $lecture): int
    {
        $minPercent = $lecture->min_watch_percent_to_unlock_next;

        return $minPercent !== null ? (int) $minPercent : 90;
    }

    /**
     * نسبة دقيقة: 100% فقط عندما completed === total.
     */
    public function percentFromCounts(int $completed, int $total): float
    {
        if ($total <= 0) {
            return 0.0;
        }

        if ($completed >= $total) {
            return 100.0;
        }

        return round(($completed / $total) * 100, 2);
    }

    public function isFinishedPercent(float $progress): bool
    {
        return $progress >= 100.0;
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
            $progress = $this->percentFromCounts($completed, $total);

            return [$progress, $total, $completed];
        }

        $totalItems = 0;
        $completedItems = 0;

        foreach ($sections as $section) {
            $items = $section->relationLoaded('activeItems')
                ? $section->activeItems
                : ($section->items ?? collect());

            foreach ($items as $item) {
                // كل عنصر منهج نشط يدخل المقام — العناصر المكسورة/غير المعروفة تُحسب ناقصة (fail-closed للشهادات)
                $totalItems++;
                $entity = $item->item ?? null;
                if ($entity && $this->isRecognizedCurriculumEntity($entity) && $this->isItemCompletedForUser($entity, $user)) {
                    $completedItems++;
                }
            }
        }

        $progress = $this->percentFromCounts($completedItems, $totalItems);

        return [$progress, $totalItems, $completedItems];
    }

    public function getCourseProgress(User $user, AdvancedCourse $course, Collection $sections): float
    {
        $this->loadCurriculumProgressForUser($sections, $user);
        [$progress] = $this->calculateFromSections($user, $course, $sections);

        return (float) $progress;
    }

    /**
     * إعادة حساب تقدّم المنهج وتخزينه في التسجيل.
     *
     * @return array{0: float, 1: int, 2: int} [progress%, total, completed]
     */
    public function recalculateAndSync(
        User $user,
        AdvancedCourse $course,
        Collection $sections,
        bool $allowDecrease = false,
    ): array {
        $this->loadCurriculumProgressForUser($sections, $user);
        [$progress, $total, $completed] = $this->calculateFromSections($user, $course, $sections);
        $this->syncEnrollmentProgress((int) $user->id, (int) $course->id, (float) $progress, $allowDecrease);

        return [(float) $progress, (int) $total, (int) $completed];
    }

    /**
     * تحديث النسبة المخزّنة في التسجيل + تعليم اكتمال المنهج بدون تغيير status
     * (حتى يبقى الطالب داخل activeCourses للشهادة لاحقاً).
     */
    public function syncEnrollmentProgress(int $userId, int $courseId, float $progress, bool $allowDecrease = false): void
    {
        $progress = round(min(100, max(0, $progress)), 2);
        $finished = $this->isFinishedPercent($progress);

        $enrollment = StudentCourseEnrollment::query()
            ->where('user_id', $userId)
            ->where('advanced_course_id', $courseId)
            ->first();

        if (! $enrollment) {
            return;
        }

        $current = $enrollment->progress !== null ? (float) $enrollment->progress : null;

        if (! $allowDecrease && $current !== null && $current >= $progress) {
            // لو النسبة أصلاً 100 ومفيش تاريخ اكتمال — ثبّته الآن
            if ($current >= 100.0 && $enrollment->curriculum_completed_at === null) {
                $enrollment->forceFill(['curriculum_completed_at' => now()])->save();
            }

            return;
        }

        $payload = ['progress' => $progress];

        if ($finished) {
            $payload['curriculum_completed_at'] = $enrollment->curriculum_completed_at ?? now();
        } elseif ($allowDecrease) {
            $payload['curriculum_completed_at'] = null;
        }

        $enrollment->forceFill($payload)->save();
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

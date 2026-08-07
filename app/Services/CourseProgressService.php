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

    /**
     * تفصيل موقف الطالب في المنهج (للوحة الإدارة).
     *
     * @return array{
     *   progress: float,
     *   total: int,
     *   completed: int,
     *   remaining: int,
     *   next_item: ?array{type: string, type_label: string, title: string, section: string},
     *   sections: list<array{
     *     id: int,
     *     title: string,
     *     completed: int,
     *     total: int,
     *     items: list<array{
     *       type: string,
     *       type_label: string,
     *       title: string,
     *       completed: bool,
     *       detail: ?string,
     *       missing: bool
     *     }>
     *   }>
     * }
     */
    public function buildProgressBreakdown(User $user, AdvancedCourse $course, Collection $sections): array
    {
        $this->loadCurriculumProgressForUser($sections, $user);

        $sectionRows = [];
        $totalItems = 0;
        $completedItems = 0;
        $nextItem = null;

        foreach ($sections as $section) {
            $items = $section->relationLoaded('activeItems')
                ? $section->activeItems
                : ($section->items ?? collect());

            $rowItems = [];
            $sectionCompleted = 0;
            $sectionTotal = 0;

            foreach ($items as $curriculumItem) {
                $entity = $curriculumItem->item ?? null;
                $sectionTotal++;
                $totalItems++;

                $type = $this->entityTypeKey($entity);
                $typeLabel = $this->entityTypeLabel($type);
                $title = $entity
                    ? (string) ($entity->title ?? $entity->name ?? ('#'.($entity->id ?? '')))
                    : 'عنصر غير موجود في المنهج';
                $completed = $entity
                    && $this->isRecognizedCurriculumEntity($entity)
                    && $this->isItemCompletedForUser($entity, $user);
                $detail = $entity ? $this->itemProgressDetail($entity, $user) : 'مورف مكسور أو محذوف';

                if ($completed) {
                    $sectionCompleted++;
                    $completedItems++;
                } elseif ($nextItem === null) {
                    $nextItem = [
                        'type' => $type,
                        'type_label' => $typeLabel,
                        'title' => $title,
                        'section' => (string) ($section->title ?? ''),
                    ];
                }

                $rowItems[] = [
                    'type' => $type,
                    'type_label' => $typeLabel,
                    'title' => $title,
                    'completed' => $completed,
                    'detail' => $detail,
                    'missing' => ! $entity,
                ];
            }

            if ($sectionTotal === 0) {
                continue;
            }

            $sectionRows[] = [
                'id' => (int) $section->id,
                'title' => (string) ($section->title ?? 'قسم'),
                'completed' => $sectionCompleted,
                'total' => $sectionTotal,
                'items' => $rowItems,
            ];
        }

        // Fallback بدون أقسام: دروس الكورس فقط
        if ($sectionRows === [] && $sections->isEmpty()) {
            $lessons = $course->lessons()->where('is_active', true)->orderBy('order')->get();
            $rowItems = [];
            foreach ($lessons as $lesson) {
                $totalItems++;
                $p = LessonProgress::query()
                    ->where('user_id', $user->id)
                    ->where('course_lesson_id', $lesson->id)
                    ->first();
                $completed = $p && $p->is_completed;
                if ($completed) {
                    $completedItems++;
                } elseif ($nextItem === null) {
                    $nextItem = [
                        'type' => 'lesson',
                        'type_label' => 'درس',
                        'title' => (string) $lesson->title,
                        'section' => 'دروس الكورس',
                    ];
                }
                $rowItems[] = [
                    'type' => 'lesson',
                    'type_label' => 'درس',
                    'title' => (string) $lesson->title,
                    'completed' => (bool) $completed,
                    'detail' => $p
                        ? ((int) $p->progress_percent).'%'.($p->is_completed ? ' · مكتمل' : '')
                        : 'لم يبدأ',
                    'missing' => false,
                ];
            }
            if ($rowItems !== []) {
                $sectionRows[] = [
                    'id' => 0,
                    'title' => 'دروس الكورس',
                    'completed' => $completedItems,
                    'total' => $totalItems,
                    'items' => $rowItems,
                ];
            }
        }

        return [
            'progress' => $this->percentFromCounts($completedItems, $totalItems),
            'total' => $totalItems,
            'completed' => $completedItems,
            'remaining' => max(0, $totalItems - $completedItems),
            'next_item' => $nextItem,
            'sections' => $sectionRows,
        ];
    }

    private function entityTypeKey(mixed $entity): string
    {
        if ($entity instanceof Lecture) {
            return 'lecture';
        }
        if ($entity instanceof CourseLesson) {
            return 'lesson';
        }
        if ($entity instanceof Assignment) {
            return 'assignment';
        }
        if ($entity instanceof LearningPattern) {
            return 'pattern';
        }
        if ($entity instanceof AdvancedExam || $entity instanceof Exam) {
            return 'exam';
        }

        return 'unknown';
    }

    private function entityTypeLabel(string $type): string
    {
        return match ($type) {
            'lecture' => 'محاضرة',
            'lesson' => 'درس',
            'assignment' => 'واجب',
            'pattern' => 'نمط تعلّم',
            'exam' => 'امتحان',
            default => 'غير معروف',
        };
    }

    private function itemProgressDetail(mixed $entity, User $user): ?string
    {
        if ($entity instanceof Lecture) {
            $wp = $this->watchProgressForUser($entity, $user);
            $threshold = $this->lectureCompletionThreshold($entity);
            if (! $wp) {
                return 'لم يشاهد بعد · مطلوب ≥'.$threshold.'%';
            }

            return (int) $wp->progress_percent.'% مشاهدة'
                .( $wp->is_completed || (int) $wp->progress_percent >= $threshold ? ' · مكتمل' : ' · مطلوب ≥'.$threshold.'%');
        }

        if ($entity instanceof CourseLesson) {
            $p = $this->lessonProgressForUser($entity, $user);
            if (! $p) {
                return 'لم يبدأ';
            }

            return (int) $p->progress_percent.'%'.($p->is_completed ? ' · مكتمل' : '');
        }

        if ($entity instanceof Assignment) {
            $has = $entity->relationLoaded('submissions')
                ? $entity->submissions->where('student_id', $user->id)->isNotEmpty()
                : $entity->submissions()->where('student_id', $user->id)->exists();

            return $has ? 'تم التسليم' : 'لم يُسلَّم';
        }

        if ($entity instanceof LearningPattern) {
            $best = $entity->getUserBestAttempt($user->id);

            return $best ? ('محاولة: '.$best->status) : 'لا توجد محاولة';
        }

        if ($entity instanceof AdvancedExam || $entity instanceof Exam) {
            $passing = (float) ($entity->passing_marks ?? 0);
            $attempt = $entity->attempts()
                ->where('user_id', $user->id)
                ->whereNotNull('submitted_at')
                ->orderByDesc('score')
                ->first();
            if (! $attempt) {
                return 'لم يؤدِّ الامتحان · النجاح من '.$passing;
            }

            $score = $attempt->score !== null ? (float) $attempt->score : null;

            return 'درجة: '.($score ?? '—').' · مطلوب ≥'.$passing
                .($score !== null && $score >= $passing ? ' · ناجح' : ' · لم ينجح');
        }

        return null;
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

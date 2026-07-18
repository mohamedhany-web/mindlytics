<?php

namespace App\Support;

use App\Models\AdvancedCourse;
use App\Models\Exam;
use App\Models\LectureAssignment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds the Student Learning OS view model from Phase 1–2 architecture.
 * Does not invent a new layout — only supplies zone data.
 */
class StudentLearningOsBuilder
{
    public function build(
        User $user,
        Collection $activeCourses,
        Collection $upcomingAssignments,
        Collection $upcomingExams,
        Collection $recentExamAttempts,
        Collection $recentCertificates,
        Collection $offlineActiveEnrollments,
        Collection $onlineActiveEnrollments,
        Collection $pendingScholarshipRegistrations,
        array $stats,
    ): array {
        $locale = app()->getLocale();
        $hour = (int) now()->format('G');
        $greeting = $hour < 12
            ? __('los.greeting_morning')
            : ($hour < 18 ? __('los.greeting_afternoon') : __('los.greeting_evening'));

        $stage = $this->resolveStage(
            $user,
            $activeCourses,
            $upcomingAssignments,
            $upcomingExams,
            $offlineActiveEnrollments,
            $onlineActiveEnrollments,
            $pendingScholarshipRegistrations
        );

        $mission = $this->resolveMission($stage, $upcomingAssignments, $upcomingExams);
        $streak = $this->resolveStreak($recentExamAttempts, $activeCourses);
        $ai = $this->resolveAi($stage, $stats, $upcomingExams, $activeCourses);
        $journey = $this->resolveJourney($activeCourses, $stage);
        $skillTree = $this->resolveSkillTree($activeCourses);
        $planning = $this->resolvePlanning($upcomingAssignments, $upcomingExams);
        $calendar = $this->resolveCalendarWeek($upcomingAssignments, $upcomingExams);
        $mastery = $this->resolveMastery($stats, $recentCertificates, $recentExamAttempts);
        $heatmap = $this->resolveHeatmap($recentExamAttempts);
        $timeline = $this->resolveTimeline($recentExamAttempts, $recentCertificates, $activeCourses);
        $courses = $this->resolveCourseStrip($activeCourses, $offlineActiveEnrollments, $onlineActiveEnrollments);
        $quickActions = $this->resolveQuickActions();
        $notes = $this->resolveNotesPlaceholder($activeCourses);
        $goals = $this->resolveGoals($stats, $stage, $streak);

        $dateFormat = $locale === 'ar' ? 'l، d F Y' : 'l, d F Y';

        return [
            'greeting' => $greeting,
            'student_name' => $user->name,
            'date_label' => now()->locale($locale)->translatedFormat($dateFormat),
            'stage' => $stage,
            'mission' => $mission,
            'streak_days' => $streak,
            'ai' => $ai,
            'journey' => $journey,
            'skill_tree' => $skillTree,
            'planning' => $planning,
            'calendar' => $calendar,
            'mastery' => $mastery,
            'heatmap' => $heatmap,
            'timeline' => $timeline,
            'courses' => $courses,
            'quick_actions' => $quickActions,
            'notes' => $notes,
            'goals' => $goals,
            'stats' => $stats,
        ];
    }

    private function locale(): string
    {
        return app()->getLocale();
    }

    private function resolveGoals(array $stats, array $stage, int $streak): array
    {
        $weeklyTarget = 3;
        $weeklyDone = min($weeklyTarget, max(0, $streak > 0 ? min($streak, $weeklyTarget) : (($stage['mode'] ?? '') === 'continue' ? 1 : 0)));

        return [
            'weekly' => [
                'label' => __('los.goal_week'),
                'title' => __('los.goal_week_title', ['count' => $weeklyTarget]),
                'done' => $weeklyDone,
                'target' => $weeklyTarget,
                'percent' => (int) round(($weeklyDone / $weeklyTarget) * 100),
            ],
            'monthly' => [
                'label' => __('los.goal_month'),
                'title' => __('los.goal_month_title'),
                'done' => (int) min(60, (float) ($stats['total_progress'] ?? 0)),
                'target' => 60,
                'percent' => (int) min(100, round(((float) ($stats['total_progress'] ?? 0) / 60) * 100)),
            ],
            'career' => [
                'label' => __('los.career_path'),
                'title' => __('los.career_title', ['count' => (int) ($stats['completed_courses'] ?? 0)]),
                'url' => route('student.certificates.index'),
            ],
        ];
    }

    private function resolveStage(
        User $user,
        Collection $activeCourses,
        Collection $upcomingAssignments,
        Collection $upcomingExams,
        Collection $offlineActiveEnrollments,
        Collection $onlineActiveEnrollments,
        Collection $pendingScholarshipRegistrations,
    ): array {
        if ($pendingScholarshipRegistrations->isNotEmpty()) {
            $reg = $pendingScholarshipRegistrations->first();

            return [
                'mode' => 'blocking',
                'type_label' => __('los.stage_activation_needed'),
                'title' => $reg->program?->name ?: __('los.scholarship_fallback'),
                'parent' => __('los.awaiting_admin'),
                'why' => __('los.path_after_activation'),
                'urgency' => null,
                'cta_label' => __('los.view_scholarship_status'),
                'cta_url' => route('notifications'),
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        $urgentExam = $upcomingExams->first(function (Exam $exam) {
            $when = $exam->start_time ?? $exam->start_date ?? $exam->end_time;

            return $when && Carbon::parse($when)->lte(now()->addDays(3));
        });

        if ($urgentExam) {
            $when = $urgentExam->start_time ?? $urgentExam->start_date;

            return [
                'mode' => 'exam',
                'type_label' => __('los.stage_exam_soon'),
                'title' => $urgentExam->title ?? $urgentExam->name ?? __('los.exam_fallback'),
                'parent' => $urgentExam->course?->title ?? $urgentExam->course?->name ?? __('los.your_course'),
                'why' => __('los.exam_why'),
                'urgency' => $when ? Carbon::parse($when)->locale($this->locale())->diffForHumans() : null,
                'cta_label' => __('los.start_prep'),
                'cta_url' => route('student.exams.show', $urgentExam->id),
                'secondary_label' => __('los.view_all_exams'),
                'secondary_url' => route('student.exams.index'),
            ];
        }

        $urgentAssignment = $upcomingAssignments->first(function (LectureAssignment $a) {
            return $a->due_date && Carbon::parse($a->due_date)->lte(now()->addDays(3));
        });

        if ($urgentAssignment) {
            $courseId = $urgentAssignment->lecture?->course_id;

            return [
                'mode' => 'assignment',
                'type_label' => __('los.stage_assignment_due'),
                'title' => $urgentAssignment->title ?? __('los.assignment_fallback'),
                'parent' => $urgentAssignment->lecture?->course?->title
                    ?? $urgentAssignment->lecture?->course?->name
                    ?? __('los.your_course'),
                'why' => __('los.assignment_why'),
                'urgency' => $urgentAssignment->due_date
                    ? Carbon::parse($urgentAssignment->due_date)->locale($this->locale())->diffForHumans()
                    : null,
                'cta_label' => __('los.complete_assignment'),
                'cta_url' => $courseId
                    ? route('my-courses.show', $courseId)
                    : route('student.assignments.index'),
                'secondary_label' => __('los.all_assignments'),
                'secondary_url' => route('student.assignments.index'),
            ];
        }

        $resume = $activeCourses
            ->sortBy(fn ($c) => (float) ($c->pivot->progress ?? optional($c->enrollment)->progress ?? 0))
            ->first(fn ($c) => (float) ($c->pivot->progress ?? optional($c->enrollment)->progress ?? 0) < 100);

        if ($resume) {
            $progress = (float) ($resume->pivot->progress ?? optional($resume->enrollment)->progress ?? 0);

            return [
                'mode' => 'continue',
                'type_label' => __('los.stage_continue'),
                'title' => $resume->title ?? $resume->name ?? __('los.current_course'),
                'parent' => $resume->academicSubject?->name ?? __('los.learning_path'),
                'why' => $progress > 0
                    ? __('los.paused_at', ['pct' => $progress])
                    : __('los.start_next_in_course'),
                'urgency' => null,
                'cta_label' => __('los.continue_now'),
                'cta_url' => route('my-courses.learn', $resume->id),
                'secondary_label' => __('los.course_details'),
                'secondary_url' => route('my-courses.show', $resume->id),
                'progress' => $progress,
            ];
        }

        if ($offlineActiveEnrollments->isNotEmpty()) {
            $en = $offlineActiveEnrollments->first();

            return [
                'mode' => 'offline',
                'type_label' => __('los.stage_offline'),
                'title' => $en->course?->title ?? $en->course?->name ?? __('los.offline_course'),
                'parent' => $en->group?->name ?? __('los.offline_path'),
                'why' => __('los.offline_why'),
                'urgency' => null,
                'cta_label' => __('los.open_portal'),
                'cta_url' => route('student.offline-courses.index'),
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        if ($onlineActiveEnrollments->isNotEmpty()) {
            $en = $onlineActiveEnrollments->first();

            return [
                'mode' => 'online',
                'type_label' => __('los.stage_online'),
                'title' => $en->course?->title ?? $en->course?->name ?? __('los.online_course'),
                'parent' => __('los.online_path'),
                'why' => __('los.online_why'),
                'urgency' => null,
                'cta_label' => __('los.open_portal'),
                'cta_url' => route('student.online-courses.index'),
                'secondary_label' => null,
                'secondary_url' => null,
            ];
        }

        $exploreRoute = \Illuminate\Support\Facades\Route::has('academic-years.index')
            ? route('academic-years.index')
            : route('academic-years');

        return [
            'mode' => 'empty',
            'type_label' => __('los.stage_start'),
            'title' => __('los.no_active_learning'),
            'parent' => 'Mindlytics Learning OS',
            'why' => __('los.empty_why'),
            'urgency' => null,
            'cta_label' => __('los.explore_courses'),
            'cta_url' => $exploreRoute,
            'secondary_label' => __('los.my_courses'),
            'secondary_url' => route('my-courses.index'),
        ];
    }

    private function resolveMission(array $stage, Collection $assignments, Collection $exams): array
    {
        if ($stage['mode'] === 'blocking') {
            return [
                'title' => __('los.mission_wait'),
                'state' => 'waiting',
                'state_label' => __('los.pending'),
                'hint' => __('los.mission_after_activation'),
            ];
        }

        if (in_array($stage['mode'], ['exam', 'assignment'], true)) {
            return [
                'title' => $stage['title'],
                'state' => 'open',
                'state_label' => __('los.mission_open'),
                'hint' => __('los.mission_close_hint'),
            ];
        }

        if ($stage['mode'] === 'continue') {
            return [
                'title' => __('los.mission_continue_title', ['title' => $stage['title'] ?? __('los.your_course')]),
                'state' => 'open',
                'state_label' => __('los.mission_open'),
                'hint' => __('los.mission_progress_hint'),
            ];
        }

        if ($exams->isNotEmpty() || $assignments->isNotEmpty()) {
            return [
                'title' => __('los.mission_schedule_title'),
                'state' => 'open',
                'state_label' => __('los.mission_open'),
                'hint' => __('los.mission_schedule_hint'),
            ];
        }

        return [
            'title' => __('los.mission_enroll_title'),
            'state' => 'open',
            'state_label' => __('los.mission_open'),
            'hint' => __('los.mission_enroll_hint'),
        ];
    }

    private function resolveStreak(Collection $attempts, Collection $courses): int
    {
        $days = $attempts
            ->pluck('created_at')
            ->filter()
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->sort()
            ->values();

        if ($days->isEmpty()) {
            return $courses->isNotEmpty() ? 1 : 0;
        }

        $streak = 1;
        $cursor = Carbon::parse($days->last());
        for ($i = $days->count() - 2; $i >= 0; $i--) {
            $prev = Carbon::parse($days[$i]);
            if ($cursor->copy()->subDay()->isSameDay($prev)) {
                $streak++;
                $cursor = $prev;
            } else {
                break;
            }
        }

        return min($streak, 30);
    }

    private function resolveAi(array $stage, array $stats, Collection $exams, Collection $courses): array
    {
        if ($stage['mode'] === 'exam') {
            return [
                'insight' => __('los.ai_exam_insight'),
                'why' => __('los.ai_exam_why'),
                'action_label' => __('los.ai_exam_action'),
                'action_url' => $stage['cta_url'],
                'recommendations' => [
                    __('los.ai_exam_rec_1'),
                    __('los.ai_exam_rec_2'),
                    __('los.ai_exam_rec_3'),
                ],
            ];
        }

        if ($stage['mode'] === 'assignment') {
            return [
                'insight' => __('los.ai_assign_insight'),
                'why' => __('los.ai_assign_why'),
                'action_label' => __('los.ai_assign_action'),
                'action_url' => $stage['cta_url'],
                'recommendations' => [
                    __('los.ai_assign_rec_1'),
                    __('los.ai_assign_rec_2'),
                    __('los.ai_assign_rec_3'),
                ],
            ];
        }

        $progress = (float) ($stats['total_progress'] ?? 0);
        if ($progress > 0 && $progress < 40 && $courses->isNotEmpty()) {
            return [
                'insight' => __('los.ai_slow_insight'),
                'why' => __('los.ai_slow_why', ['pct' => $progress]),
                'action_label' => __('los.ai_slow_action'),
                'action_url' => $stage['cta_url'] ?? route('my-courses.index'),
                'recommendations' => [
                    __('los.ai_slow_rec_1'),
                    __('los.ai_slow_rec_2'),
                    __('los.ai_slow_rec_3'),
                ],
            ];
        }

        if ($stage['mode'] === 'continue') {
            return [
                'insight' => __('los.ai_continue_insight'),
                'why' => __('los.ai_continue_why'),
                'action_label' => __('los.ai_continue_action'),
                'action_url' => $stage['cta_url'],
                'recommendations' => [
                    __('los.ai_continue_rec_1'),
                    __('los.ai_continue_rec_2'),
                    __('los.ai_continue_rec_3'),
                ],
            ];
        }

        $exploreRoute = \Illuminate\Support\Facades\Route::has('academic-years.index')
            ? route('academic-years.index')
            : route('academic-years');

        return [
            'insight' => __('los.ai_empty_insight'),
            'why' => __('los.ai_empty_why'),
            'action_label' => __('los.explore_courses'),
            'action_url' => $exploreRoute,
            'recommendations' => [
                __('los.ai_empty_rec_1'),
                __('los.ai_empty_rec_2'),
                __('los.ai_empty_rec_3'),
            ],
        ];
    }

    private function resolveJourney(Collection $courses, array $stage): array
    {
        if ($courses->isEmpty()) {
            return [
                'past' => __('los.journey_start'),
                'present' => __('los.journey_choose_path'),
                'next' => __('los.journey_first_lesson'),
                'recovery' => null,
            ];
        }

        /** @var AdvancedCourse $course */
        $course = $courses->first();
        $progress = (float) ($course->pivot->progress ?? optional($course->enrollment)->progress ?? 0);

        $past = $progress >= 30 ? __('los.journey_basics_done') : __('los.journey_joined');
        $present = $stage['title'] ?? ($course->title ?? __('los.journey_current'));
        $next = $progress >= 70 ? __('los.journey_capstone') : __('los.journey_next_unit');

        $recovery = $progress > 0 && $progress < 25
            ? __('los.journey_recovery')
            : null;

        return compact('past', 'present', 'next', 'recovery');
    }

    private function resolveSkillTree(Collection $courses): array
    {
        $nodes = [];
        foreach ($courses->take(4) as $course) {
            $p = (float) ($course->pivot->progress ?? optional($course->enrollment)->progress ?? 0);
            $level = $p >= 75
                ? __('los.skill_mastered')
                : ($p >= 40 ? __('los.skill_growing') : __('los.skill_beginner'));
            $nodes[] = [
                'label' => $course->academicSubject?->name ?? ($course->title ?? __('los.skill_fallback')),
                'level' => $level,
                'progress' => $p,
            ];
        }

        if ($nodes === []) {
            $nodes[] = [
                'label' => __('los.skill_yours'),
                'level' => __('los.skill_waiting'),
                'progress' => 0,
            ];
        }

        return $nodes;
    }

    private function resolvePlanning(Collection $assignments, Collection $exams): array
    {
        $items = collect();
        $locale = $this->locale();

        foreach ($exams->take(4) as $exam) {
            $when = $exam->start_time ?? $exam->start_date ?? $exam->end_time;
            $items->push([
                'kind' => 'exam',
                'title' => $exam->title ?? $exam->name ?? __('los.exam_fallback'),
                'when' => $when
                    ? Carbon::parse($when)->locale($locale)->diffForHumans()
                    : __('los.soon'),
                'url' => route('student.exams.show', $exam->id),
            ]);
        }

        foreach ($assignments->take(4) as $a) {
            $items->push([
                'kind' => 'assignment',
                'title' => $a->title ?? __('los.assignment_fallback'),
                'when' => $a->due_date
                    ? Carbon::parse($a->due_date)->locale($locale)->diffForHumans()
                    : __('los.no_due_date'),
                'url' => route('student.assignments.index'),
            ]);
        }

        return $items->take(5)->values()->all();
    }

    private function resolveCalendarWeek(Collection $assignments, Collection $exams): array
    {
        $locale = $this->locale();
        $weekStartsAt = $locale === 'ar' ? Carbon::SATURDAY : Carbon::MONDAY;
        $start = now()->startOfWeek($weekStartsAt);
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $has = $exams->contains(function ($exam) use ($day) {
                $when = $exam->start_time ?? $exam->start_date;

                return $when && Carbon::parse($when)->isSameDay($day);
            }) || $assignments->contains(function ($a) use ($day) {
                return $a->due_date && Carbon::parse($a->due_date)->isSameDay($day);
            });

            $days[] = [
                'label' => $day->locale($locale)->translatedFormat('D'),
                'num' => $day->format('d'),
                'today' => $day->isToday(),
                'has_event' => $has,
            ];
        }

        return $days;
    }

    private function resolveMastery(array $stats, Collection $certs, Collection $attempts): array
    {
        $cert = $certs->first();
        $attempt = $attempts->first();

        return [
            'progress' => (float) ($stats['total_progress'] ?? 0),
            'completed_courses' => (int) ($stats['completed_courses'] ?? 0),
            'whisper' => $cert
                ? __('los.recent_cert', ['title' => $cert->course?->title ?? $cert->course?->name ?? __('los.achievement')])
                : ($attempt
                    ? __('los.last_exam_attempt')
                    : __('los.achievements_appear')),
            'achievement_url' => route('student.certificates.index'),
        ];
    }

    private function resolveHeatmap(Collection $attempts): array
    {
        $map = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $count = $attempts->filter(fn ($a) => $a->created_at && Carbon::parse($a->created_at)->toDateString() === $day)->count();
            $map[] = [
                'date' => $day,
                'level' => min(3, $count),
            ];
        }

        return $map;
    }

    private function resolveTimeline(Collection $attempts, Collection $certs, Collection $courses): array
    {
        $events = collect();
        $locale = $this->locale();

        foreach ($attempts->take(3) as $a) {
            $events->push([
                'label' => __('los.timeline_exam', ['title' => $a->exam?->title ?? __('los.exam_fallback')]),
                'when' => optional($a->created_at)?->locale($locale)->diffForHumans(),
            ]);
        }

        foreach ($certs->take(2) as $c) {
            $events->push([
                'label' => __('los.timeline_cert', ['title' => $c->course?->title ?? __('los.achievement')]),
                'when' => optional($c->issued_at ?? $c->created_at)?->locale($locale)->diffForHumans(),
            ]);
        }

        if ($events->isEmpty() && $courses->isNotEmpty()) {
            $events->push([
                'label' => __('los.timeline_joined'),
                'when' => __('los.recently'),
            ]);
        }

        return $events->take(5)->values()->all();
    }

    private function resolveCourseStrip(Collection $courses, Collection $offline, Collection $online): array
    {
        $items = collect();
        $pct = app()->getLocale() === 'ar' ? '٪' : '%';

        foreach ($courses->take(4) as $c) {
            $items->push([
                'title' => $c->title ?? $c->name,
                'meta' => round((float) ($c->pivot->progress ?? optional($c->enrollment)->progress ?? 0)).$pct,
                'url' => route('my-courses.show', $c->id),
            ]);
        }

        foreach ($offline->take(2) as $en) {
            $items->push([
                'title' => $en->course?->title ?? __('los.offline_course'),
                'meta' => __('los.meta_offline'),
                'url' => route('student.offline-courses.index'),
            ]);
        }

        foreach ($online->take(2) as $en) {
            $items->push([
                'title' => $en->course?->title ?? __('los.online_course'),
                'meta' => __('los.meta_online'),
                'url' => route('student.online-courses.index'),
            ]);
        }

        return $items->take(6)->values()->all();
    }

    private function resolveQuickActions(): array
    {
        return [
            ['label' => __('los.qa_my_courses'), 'url' => route('my-courses.index')],
            ['label' => __('los.qa_exams'), 'url' => route('student.exams.index')],
            ['label' => __('los.qa_assignments'), 'url' => route('student.assignments.index')],
            ['label' => __('los.qa_calendar'), 'url' => route('calendar')],
            ['label' => __('los.qa_certificates'), 'url' => route('student.certificates.index')],
            ['label' => __('los.qa_groups'), 'url' => route('student.groups.index')],
        ];
    }

    private function resolveNotesPlaceholder(Collection $courses): array
    {
        if ($courses->isEmpty()) {
            return [
                ['label' => __('los.notes_placeholder'), 'url' => route('my-courses.index')],
            ];
        }

        return $courses->take(3)->map(function ($c) {
            return [
                'label' => __('los.notes_review', ['title' => $c->title ?? $c->name]),
                'url' => route('my-courses.learn', $c->id),
            ];
        })->values()->all();
    }
}

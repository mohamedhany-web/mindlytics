<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * عرض قائمة الامتحانات المتاحة للطالب (أونلاين + أوفلاين)
     */
    public function index()
    {
        $user = Auth::user();

        $onlineCourseIds = $user->activeCourses()->pluck('advanced_courses.id');
        $offlineCourseIds = $user->offlineEnrollments()->where('status', 'active')->pluck('offline_course_id');

        $availableExams = Exam::where(function ($q) use ($onlineCourseIds, $offlineCourseIds) {
            $q->whereIn('advanced_course_id', $onlineCourseIds)
                ->orWhereIn('offline_course_id', $offlineCourseIds);
        })
            ->available()
            ->with(['course.academicSubject', 'lesson', 'offlineCourse'])
            ->orderBy('created_at', 'desc')
            ->get();

        $availableExams->each(function ($exam) use ($user) {
            $this->enrichExam($exam, $user);
        });

        $completedExams = $availableExams->filter(fn ($exam) => $exam->last_attempt && $exam->last_attempt->status === 'completed');

        $stats = [
            'total' => $availableExams->count(),
            'available' => $availableExams->where('portal_status', 'available')->count(),
            'in_progress' => $availableExams->where('portal_status', 'in_progress')->count(),
            'completed' => $completedExams->count(),
            'avg_score' => round((float) $completedExams->whereNotNull('best_percentage')->avg('best_percentage'), 1),
        ];

        $moduleStats = $this->buildModuleStats($availableExams);

        return view('student.exams.index', compact('availableExams', 'stats', 'moduleStats'));
    }

    /**
     * عرض تفاصيل الامتحان قبل البدء
     */
    public function show(Exam $exam)
    {
        $user = Auth::user();

        // التحقق من إمكانية الوصول (أونلاين أو أوفلاين)
        $canAccess = ($exam->advanced_course_id && $user->isEnrolledIn($exam->advanced_course_id))
            || ($exam->offline_course_id && $user->isEnrolledInOfflineCourse($exam->offline_course_id));
        if (!$canAccess) {
            return redirect()->route('my-courses.index')
                ->with('error', __('student.exam_forbidden'));
        }

        if (!$exam->isAvailable()) {
            return redirect()->route('student.exams.index')
                ->with('error', __('student.exam_not_available'));
        }

        // إذا كانت هناك محاولة جارية، إعادة التوجيه مباشرة لصفحة الامتحان (استئناف)
        $activeAttempt = $exam->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
        if ($activeAttempt) {
            return redirect()->route('student.exams.take', [$exam, $activeAttempt]);
        }

        if (!$exam->canAttempt($user->id)) {
            return redirect()->route('student.exams.index')
                ->with('error', __('student.exam_attempts_exhausted'));
        }

        $exam->load(['course.academicSubject', 'lesson', 'offlineCourse']);
        $this->enrichExam($exam, $user);

        // معلومات المحاولات السابقة
        $previousAttempts = $exam->attempts()
                               ->where('user_id', $user->id)
                               ->orderBy('created_at', 'desc')
                               ->get();

        // إذا كان الطلب AJAX، إرجاع JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'instructions' => $exam->instructions,
                'duration_minutes' => $exam->duration_minutes,
                'total_marks' => $exam->total_marks,
                'passing_marks' => $exam->passing_marks,
                'attempts_allowed' => $exam->attempts_allowed,
            ]);
        }

        return view('student.exams.show', compact('exam', 'previousAttempts'));
    }

    /**
     * بدء الامتحان
     */
    public function start(Exam $exam)
    {
        try {
            $user = Auth::user();
            $canAccess = ($exam->advanced_course_id && $user->isEnrolledIn($exam->advanced_course_id))
                || ($exam->offline_course_id && $user->isEnrolledInOfflineCourse($exam->offline_course_id));

            $activeAttempt = $exam->attempts()
                                ->where('user_id', $user->id)
                                ->where('status', 'in_progress')
                                ->first();

            if ($activeAttempt) {
                return redirect()->route('student.exams.take', [$exam, $activeAttempt]);
            }

            if (!$canAccess || !$exam->canAttempt($user->id)) {
                return redirect()->route('student.exams.index')
                    ->with('error', __('student.exam_start_forbidden'));
            }

            $validQuestionsCount = $exam->examQuestions()->whereHas('question')->count();
            if ($validQuestionsCount === 0) {
                return redirect()->route('student.exams.show', $exam)
                    ->with('error', __('student.exam_no_questions'));
            }

            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'user_id' => $user->id,
                'started_at' => now(),
                'status' => 'in_progress',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'answers' => [],
                'tab_switches' => 0,
                'suspicious_activities' => [],
            ]);

            return redirect()->route('student.exams.take', [$exam, $attempt]);

        } catch (\Throwable $e) {
            \Log::error('Exam start failed', [
                'exam_id' => $exam->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('student.exams.index')
                ->with('error', __('student.exam_start_error'));
        }
    }

    /**
     * أداء الامتحان
     */
    public function take(Exam $exam, ExamAttempt $attempt)
    {
        try {
            $user = Auth::user();

            if ((int) $attempt->user_id !== (int) $user->id || (int) $attempt->exam_id !== (int) $exam->id) {
                return redirect()->route('student.exams.index')
                    ->with('error', __('student.exam_attempt_forbidden'));
            }

            if ($attempt->status !== 'in_progress') {
                return redirect()->route('student.exams.result', [$exam, $attempt]);
            }

            // التحقق من انتهاء الوقت
            if ($attempt->isTimeExpired()) {
                return $this->autoSubmit($exam, $attempt);
            }

            $exam->load(['examQuestions.question.category', 'course', 'offlineCourse']);
            $this->enrichExam($exam, $user);

            $questions = $exam->examQuestions()->with(['question.category'])->whereHas('question')->orderBy('order')->get();

            if ($questions->isEmpty()) {
                return redirect()->route('student.exams.show', $exam)
                    ->with('error', __('student.exam_no_questions_contact'));
            }

            // ترتيب الأسئلة
            if ($exam->randomize_questions) {
                $questions = $questions->shuffle();
            }

            return view('student.exams.take', compact('exam', 'attempt', 'questions'));
        } catch (\Throwable $e) {
            \Log::error('Exam take page failed', [
                'exam_id' => $exam->id ?? null,
                'attempt_id' => $attempt->id ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('student.exams.index')
                ->with('error', __('student.exam_take_error'));
        }
    }

    /**
     * حفظ إجابة
     */
    public function saveAnswer(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ((int) $attempt->user_id !== (int) $user->id || $attempt->status !== 'in_progress') {
            return response()->json(['error' => __('student.exam_attempt_forbidden')], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|integer',
            'answer' => 'nullable',
        ]);

        $questionBelongsToExam = $exam->examQuestions()
            ->where('question_id', $validated['question_id'])
            ->whereHas('question')
            ->exists();

        if (!$questionBelongsToExam) {
            return response()->json(['error' => __('student.exam_invalid_question')], 422);
        }

        if ($attempt->isTimeExpired()) {
            return response()->json(['error' => __('student.exam_time_expired')], 410);
        }

        $answers = $attempt->answers ?? [];
        $answers[$validated['question_id']] = $validated['answer'];

        $attempt->update(['answers' => $answers]);

        return response()->json(['success' => true, 'message' => __('student.exam_answer_saved')]);
    }

    /**
     * تسليم الامتحان
     */
    public function submit(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        try {
            $user = Auth::user();

            if ((int) $attempt->user_id !== (int) $user->id || (int) $attempt->exam_id !== (int) $exam->id) {
                return redirect()->route('student.exams.index')
                    ->with('error', __('student.exam_submit_forbidden'));
            }

            if ($attempt->status === 'completed') {
                return $exam->show_results_immediately
                    ? redirect()->route('student.exams.result', [$exam, $attempt])
                    : redirect()->route('student.exams.index')->with('info', __('student.exam_already_submitted'));
            }

            if ($attempt->status !== 'in_progress') {
                return redirect()->route('student.exams.index')
                    ->with('error', __('student.exam_submit_invalid'));
            }

            return $this->completeAttempt($exam, $attempt, false);
        } catch (\Throwable $e) {
            \Log::error('Exam submit endpoint failed', [
                'exam_id' => $exam->id ?? null,
                'attempt_id' => $attempt->id ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('student.exams.index')
                ->with('error', __('student.exam_submit_error'));
        }
    }

    /**
     * تسليم تلقائي عند انتهاء الوقت
     */
    public function autoSubmit(Exam $exam, ExamAttempt $attempt)
    {
        return $this->completeAttempt($exam, $attempt, true);
    }

    /**
     * إكمال المحاولة وحساب النتيجة
     */
    private function completeAttempt(Exam $exam, ExamAttempt $attempt, $autoSubmitted = false)
    {
        try {
            DB::transaction(function () use ($exam, $attempt, $autoSubmitted) {
                $timeTaken = (int) max(0, now()->diffInSeconds($attempt->started_at, true));

                $attempt->update([
                    'status' => 'completed',
                    'submitted_at' => now(),
                    'time_taken' => $timeTaken,
                    'auto_submitted' => $autoSubmitted,
                ]);

                $attempt->refresh();
                $attempt->calculateScore();
            });
        } catch (\Throwable $e) {
            \Log::error('Exam submit failed', [
                'exam_id' => $exam->id,
                'attempt_id' => $attempt->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('student.exams.index')
                ->with('error', __('student.exam_submit_error'));
        }

        if ($exam->advanced_course_id) {
            try {
                $userId = $attempt->user_id;
                app(\App\Http\Controllers\Student\MyCourseController::class)->updateCourseProgress($userId, $exam->advanced_course_id);
            } catch (\Throwable $e) {
                \Log::warning('Failed to update course progress after exam submit: ' . $e->getMessage());
            }
        }

        if ($exam->show_results_immediately) {
            return redirect()->route('student.exams.result', [$exam, $attempt]);
        }

        return redirect()->route('student.exams.index')
            ->with('success', __('student.exam_submitted_success'));
    }

    /**
     * عرض نتيجة الامتحان
     */
    public function result(Exam $exam, ExamAttempt $attempt)
    {
        try {
            $user = Auth::user();

            if ((int) $attempt->user_id !== (int) $user->id) {
                return redirect()->route('student.exams.index')
                    ->with('error', __('student.exam_result_forbidden'));
            }

            if (!$exam->show_results_immediately && $attempt->status === 'completed') {
                return redirect()->route('student.exams.index')
                    ->with('info', __('student.exam_result_later'));
            }

            $attempt->load(['exam.examQuestions.question']);
            $exam->load(['course', 'offlineCourse']);
            $this->enrichExam($exam, $user);
            $reviewQuestions = $exam->examQuestions()->with('question')->whereHas('question')->orderBy('order')->get();
            return view('student.exams.result', compact('exam', 'attempt', 'reviewQuestions'));
        } catch (\Throwable $e) {
            \Log::error('Exam result page failed', [
                'exam_id' => $exam->id ?? null,
                'attempt_id' => $attempt->id ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('student.exams.index')
                ->with('error', __('student.exam_result_error'));
        }
    }

    /**
     * تسجيل تبديل التبويب
     */
    public function logTabSwitch(Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ((int) $attempt->user_id !== (int) $user->id || $attempt->status !== 'in_progress') {
            return response()->json(['error' => __('student.exam_attempt_forbidden')], 403);
        }

        $attempt->incrementTabSwitches();

        if ($exam->prevent_tab_switch && $attempt->tab_switches >= 3) {
            $this->completeAttempt($exam, $attempt, true);

            return response()->json([
                'exam_ended' => true,
                'message' => __('student.exam_tab_switch_ended'),
            ]);
        }

        return response()->json([
            'warning' => true,
            'tab_switches' => $attempt->tab_switches,
            'message' => __('student.exam_tab_switch_warning', ['count' => $attempt->tab_switches]),
        ]);
    }

    private function resolveExamStatus(Exam $exam, $user): string
    {
        $lastAttempt = $exam->getLastAttempt($user->id);

        if ($lastAttempt && $lastAttempt->status === 'in_progress') {
            return 'in_progress';
        }

        if ($exam->canAttempt($user->id)) {
            return 'available';
        }

        if ($lastAttempt && $lastAttempt->status === 'completed') {
            return 'completed';
        }

        if ($exam->attempts_allowed > 0 && $exam->user_attempts >= $exam->attempts_allowed) {
            return 'exhausted';
        }

        return 'locked';
    }

    private function enrichExam(Exam $exam, $user): void
    {
        $exam->user_attempts = $exam->attempts()->where('user_id', $user->id)->count();
        $exam->can_attempt = $exam->canAttempt($user->id);
        $exam->last_attempt = $exam->getLastAttempt($user->id);
        $exam->best_score = $exam->getBestScore($user->id);
        $exam->best_percentage = $exam->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->max('percentage');
        $exam->portal_status = $this->resolveExamStatus($exam, $user);
        $exam->source = $this->resolveExamSource($exam, $user);
        $exam->source_label = $this->sourceLabel($exam->source);
        $exam->source_icon = $this->sourceIcon($exam->source);
        $exam->source_bubble = $this->sourceBubble($exam->source);
        $exam->course_label = $exam->offlineCourse->title ?? $exam->course->title ?? __('student.course_undefined');
        $exam->questions_count = $exam->examQuestions()->whereHas('question')->count();
        $exam->course_route = $this->courseRouteForExam($exam, $exam->source);
        $exam->module_route = $this->moduleRouteForSource($exam->source);
    }

    private function resolveExamSource(Exam $exam, $user): string
    {
        if ($exam->advanced_course_id) {
            return 'recorded';
        }

        if ($exam->offline_course_id) {
            $enrollment = $user->offlineEnrollments()
                ->where('offline_course_id', $exam->offline_course_id)
                ->where('status', 'active')
                ->first();

            if ($enrollment?->enrollment_channel === 'online') {
                return 'online';
            }

            return 'offline';
        }

        return 'recorded';
    }

    private function sourceLabel(string $source): string
    {
        return match ($source) {
            'offline' => __('student.exam_source_offline'),
            'online' => __('student.exam_source_online'),
            default => __('student.exam_source_recorded'),
        };
    }

    private function sourceIcon(string $source): string
    {
        return match ($source) {
            'offline' => 'icon-classes.svg',
            'online' => 'icon-community.svg',
            default => 'icon-courses.svg',
        };
    }

    private function sourceBubble(string $source): string
    {
        return match ($source) {
            'offline' => 'var(--sp-peach)',
            'online' => 'var(--sp-lilac)',
            default => 'var(--sp-sky)',
        };
    }

    private function courseRouteForExam(Exam $exam, string $source): string
    {
        return match ($source) {
            'offline' => $exam->offline_course_id
                ? route('student.offline-courses.show', $exam->offline_course_id)
                : route('student.offline-courses.index'),
            'online' => $exam->offline_course_id
                ? route('student.online-courses.show', $exam->offline_course_id)
                : route('student.online-courses.index'),
            default => $exam->advanced_course_id
                ? route('my-courses.show', $exam->advanced_course_id)
                : route('my-courses.index'),
        };
    }

    private function moduleRouteForSource(string $source): string
    {
        return match ($source) {
            'offline' => route('student.exams.index', ['module' => 'offline']),
            'online' => route('student.exams.index', ['module' => 'online']),
            default => route('student.exams.index', ['module' => 'recorded']),
        };
    }

    private function buildModuleStats($exams): array
    {
        $modules = ['recorded', 'offline', 'online'];
        $out = [];

        foreach ($modules as $module) {
            $subset = $exams->where('source', $module);
            $completed = $subset->filter(fn ($e) => $e->last_attempt && $e->last_attempt->status === 'completed');
            $out[$module] = [
                'total' => $subset->count(),
                'available' => $subset->where('portal_status', 'available')->count(),
                'in_progress' => $subset->where('portal_status', 'in_progress')->count(),
                'completed' => $completed->count(),
            ];
        }

        return $out;
    }
}

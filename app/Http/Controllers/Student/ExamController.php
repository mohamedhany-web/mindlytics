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

        // إضافة معلومات المحاولات لكل امتحان
        $availableExams->each(function($exam) use ($user) {
            $exam->user_attempts = $exam->attempts()->where('user_id', $user->id)->count();
            $exam->can_attempt = $exam->canAttempt($user->id);
            $exam->last_attempt = $exam->getLastAttempt($user->id);
            $exam->best_score = $exam->getBestScore($user->id);
        });

        return view('student.exams.index', compact('availableExams'));
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
                ->with('error', 'غير مصرح لك بالوصول لهذا الامتحان');
        }

        if (!$exam->isAvailable()) {
            return redirect()->route('student.exams.index')
                ->with('error', 'الامتحان غير متاح حالياً');
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
                ->with('error', 'لقد استنفدت عدد المحاولات المسموحة');
        }

        $exam->load(['course.academicSubject', 'lesson', 'offlineCourse']);
        
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
                    ->with('error', 'غير مصرح لك ببدء هذا الامتحان');
            }

            $validQuestionsCount = $exam->examQuestions()->whereHas('question')->count();
            if ($validQuestionsCount === 0) {
                return redirect()->route('student.exams.show', $exam)
                    ->with('error', 'لا توجد أسئلة صالحة في هذا الامتحان حالياً.');
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
                ->with('error', 'حدث خطأ عند بدء الامتحان. يرجى المحاولة مرة أخرى.');
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
                    ->with('error', 'غير مصرح لك بالوصول لهذه المحاولة');
            }

            if ($attempt->status !== 'in_progress') {
                return redirect()->route('student.exams.result', [$exam, $attempt]);
            }

            // التحقق من انتهاء الوقت
            if ($attempt->isTimeExpired()) {
                return $this->autoSubmit($exam, $attempt);
            }

            $exam->load(['examQuestions.question.category']);

            $questions = $exam->examQuestions()->with(['question.category'])->whereHas('question')->orderBy('order')->get();

            if ($questions->isEmpty()) {
                return redirect()->route('student.exams.show', $exam)
                    ->with('error', 'لا توجد أسئلة صالحة في هذا الامتحان حالياً. يرجى التواصل مع الإدارة.');
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
                ->with('error', 'حدث خطأ أثناء فتح صفحة الامتحان. تم تسجيل السبب وسيتم إصلاحه.');
        }
    }

    /**
     * حفظ إجابة
     */
    public function saveAnswer(Request $request, Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ((int) $attempt->user_id !== (int) $user->id || $attempt->status !== 'in_progress') {
            return response()->json(['error' => 'غير مصرح'], 403);
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
            return response()->json(['error' => 'السؤال غير صالح لهذا الامتحان'], 422);
        }

        if ($attempt->isTimeExpired()) {
            return response()->json(['error' => 'انتهى الوقت المحدد'], 410);
        }

        $answers = $attempt->answers ?? [];
        $answers[$validated['question_id']] = $validated['answer'];

        $attempt->update(['answers' => $answers]);

        return response()->json(['success' => true, 'message' => 'تم حفظ الإجابة']);
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
                    ->with('error', 'غير مصرح لك بتسليم هذا الامتحان');
            }

            if ($attempt->status === 'completed') {
                return $exam->show_results_immediately
                    ? redirect()->route('student.exams.result', [$exam, $attempt])
                    : redirect()->route('student.exams.index')->with('info', 'تم تسليم الامتحان بالفعل.');
            }

            if ($attempt->status !== 'in_progress') {
                return redirect()->route('student.exams.index')
                    ->with('error', 'حالة المحاولة غير صالحة للتسليم');
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
                ->with('error', 'حدث خطأ أثناء التسليم. يرجى المحاولة مرة أخرى.');
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
                ->with('error', 'حدث خطأ أثناء تسليم الامتحان. يرجى المحاولة مرة أخرى.');
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
            ->with('success', 'تم تسليم الامتحان بنجاح. ستظهر النتيجة لاحقاً.');
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
                    ->with('error', 'غير مصرح لك بعرض هذه النتيجة');
            }

            if (!$exam->show_results_immediately && $attempt->status === 'completed') {
                return redirect()->route('student.exams.index')
                    ->with('info', 'ستظهر النتيجة لاحقاً');
            }

            $attempt->load(['exam.examQuestions.question']);
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
                ->with('error', 'حدث خطأ أثناء عرض نتيجة الامتحان. تم تسجيل السبب وسيتم إصلاحه.');
        }
    }

    /**
     * تسجيل تبديل التبويب
     */
    public function logTabSwitch(Exam $exam, ExamAttempt $attempt)
    {
        $user = Auth::user();

        if ((int) $attempt->user_id !== (int) $user->id || $attempt->status !== 'in_progress') {
            return response()->json(['error' => 'غير مصرح'], 403);
        }

        $attempt->incrementTabSwitches();

        // إنهاء الامتحان إذا كان منع تبديل التبويبات مفعل
        if ($exam->prevent_tab_switch && $attempt->tab_switches >= 3) {
            $this->completeAttempt($exam, $attempt, true);
            return response()->json([
                'exam_ended' => true,
                'message' => 'تم إنهاء الامتحان بسبب تبديل التبويبات المتكرر'
            ]);
        }

        return response()->json([
            'warning' => true,
            'tab_switches' => $attempt->tab_switches,
            'message' => 'تحذير: تم رصد تبديل التبويب. المحاولة رقم ' . $attempt->tab_switches
        ]);
    }
}

<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedExam;
use App\Models\AdvancedCourse;
use App\Models\OfflineCourse;
use App\Models\Question;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $instructor = Auth::user();
        
        // جلب الكورسات الأونلاين والأوفلاين التي يدرسها المدرب
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        $offlineCourses = OfflineCourse::where('instructor_id', $instructor->id)
            ->orderBy('title')
            ->get();
        
        // جلب الاختبارات (أونلاين وأوفلاين)
        $query = AdvancedExam::where(function ($q) use ($instructor) {
                $q->whereHas('advancedCourse', fn($q2) => $q2->where('instructor_id', $instructor->id))
                  ->orWhereHas('offlineCourse', fn($q2) => $q2->where('instructor_id', $instructor->id));
            })
            ->with(['advancedCourse', 'offlineCourse', 'lesson'])
            ->withCount(['questions', 'attempts']);
        
        // فلترة حسب الكورس الأونلاين
        if ($request->filled('course_id')) {
            $query->where('advanced_course_id', $request->course_id);
        }
        if ($request->filled('offline_course_id')) {
            $query->where('offline_course_id', $request->offline_course_id);
        }
        
        // فلترة حسب الحالة
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }
        
        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $exams = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // إحصائيات (أونلاين + أوفلاين)
        $baseQuery = function () use ($instructor) {
            return AdvancedExam::where(function ($q) use ($instructor) {
                $q->whereHas('advancedCourse', fn($q2) => $q2->where('instructor_id', $instructor->id))
                  ->orWhereHas('offlineCourse', fn($q2) => $q2->where('instructor_id', $instructor->id));
            });
        };
        $stats = [
            'total' => (clone $baseQuery())->count(),
            'active' => (clone $baseQuery())->where('is_active', true)->count(),
            'total_attempts' => ExamAttempt::whereHas('exam', function ($q) use ($instructor) {
                $q->where(function ($q2) use ($instructor) {
                    $q2->whereHas('advancedCourse', fn($q3) => $q3->where('instructor_id', $instructor->id))
                       ->orWhereHas('offlineCourse', fn($q3) => $q3->where('instructor_id', $instructor->id));
                });
            })->count(),
            'completed_attempts' => ExamAttempt::whereHas('exam', function ($q) use ($instructor) {
                $q->where(function ($q2) use ($instructor) {
                    $q2->whereHas('advancedCourse', fn($q3) => $q3->where('instructor_id', $instructor->id))
                       ->orWhereHas('offlineCourse', fn($q3) => $q3->where('instructor_id', $instructor->id));
                });
            })->where('status', 'completed')->count(),
        ];
        
        return view('instructor.exams.index', compact('exams', 'courses', 'offlineCourses', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $instructor = Auth::user();
        
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        $offlineCourses = OfflineCourse::where('instructor_id', $instructor->id)
            ->orderBy('title')
            ->get();
        
        return view('instructor.exams.create', compact('courses', 'offlineCourses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $instructor = Auth::user();
        
        $validated = $request->validate([
            'advanced_course_id' => 'nullable|required_without:offline_course_id|exists:advanced_courses,id',
            'offline_course_id' => 'nullable|required_without:advanced_course_id|exists:offline_courses,id',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:0|max:' . ($request->total_marks ?? 100),
            'duration_minutes' => 'required|integer|min:5|max:480',
            'attempts_allowed' => 'required|integer|min:1|max:10',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_explanations' => 'boolean',
            'allow_review' => 'boolean',
            'is_active' => 'boolean',
            'sidebar_position' => 'nullable|integer|min:1|max:10',
            'show_in_sidebar' => 'boolean',
        ], [
            'advanced_course_id.required_without' => 'يجب اختيار كورس أونلاين أو أوفلاين',
            'offline_course_id.required_without' => 'يجب اختيار كورس أونلاين أو أوفلاين',
            'title.required' => 'عنوان الاختبار مطلوب',
            'total_marks.required' => 'الدرجة الكلية مطلوبة',
            'passing_marks.max' => 'درجة النجاح يجب ألا تتجاوز الدرجة الكلية',
            'duration_minutes.min' => 'المدة يجب أن تكون 5 دقائق على الأقل',
            'duration_minutes.max' => 'المدة يجب ألا تتجاوز 480 دقيقة',
        ]);
        
        if ($request->filled('offline_course_id')) {
            $offlineCourse = OfflineCourse::where('id', $request->offline_course_id)
                ->where('instructor_id', $instructor->id)
                ->firstOrFail();
            $validated['advanced_course_id'] = null;
            $validated['course_lesson_id'] = null;
            $validated['offline_course_id'] = $offlineCourse->id;
        } else {
            $course = AdvancedCourse::where('id', $validated['advanced_course_id'])
                ->where('instructor_id', $instructor->id)
                ->firstOrFail();
            $validated['offline_course_id'] = null;
            if ($validated['course_lesson_id']) {
                $lesson = \App\Models\CourseLesson::where('id', $validated['course_lesson_id'])
                    ->where('advanced_course_id', $validated['advanced_course_id'])
                    ->firstOrFail();
            }
        }
        
        $validated['created_by'] = $instructor->id;
        $validated['randomize_questions'] = $request->has('randomize_questions');
        $validated['randomize_options'] = $request->has('randomize_options');
        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['show_explanations'] = $request->has('show_explanations');
        $validated['allow_review'] = $request->has('allow_review');
        $validated['is_active'] = $request->has('is_active');
        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['show_in_sidebar'] = $request->has('show_in_sidebar');
        $validated['sidebar_position'] = $request->filled('sidebar_position') ? $request->sidebar_position : null;
        
        $exam = AdvancedExam::create($validated);
        
        // إضافة الأسئلة إذا تم إرسالها
        if ($request->filled('questions') && is_array($request->questions)) {
            foreach ($request->questions as $index => $questionId) {
                \App\Models\ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $questionId,
                    'order' => $index + 1,
                    'marks' => $request->question_marks[$questionId] ?? 1,
                ]);
            }
        }
        
        // بعد إنشاء الاختبار، إعادة التوجيه إلى صفحة إدارة الأسئلة
        return redirect()->route('instructor.exams.questions.manage', $exam)
            ->with('success', 'تم إنشاء الاختبار بنجاح. يمكنك الآن إضافة الأسئلة');
    }

    /**
     * Display the specified resource.
     */
    public function show(AdvancedExam $exam)
    {
        $instructor = Auth::user();
        
        $owns = ($exam->advancedCourse && $exam->advancedCourse->instructor_id === $instructor->id)
            || ($exam->offlineCourse && $exam->offlineCourse->instructor_id === $instructor->id);
        if (!$owns) {
            abort(403, 'غير مسموح لك بالوصول لهذا الاختبار');
        }
        
        $exam->load(['advancedCourse', 'offlineCourse', 'lesson', 'questions', 'attempts.user']);
        
        // جلب الطلاب المسجلين (أونلاين أو أوفلاين)
        if ($exam->offline_course_id) {
            $enrollments = \App\Models\OfflineCourseEnrollment::where('offline_course_id', $exam->offline_course_id)
                ->where('status', 'active')
                ->with('student')
                ->get()
                ->map(fn ($e) => (object)['user' => $e->student, 'user_id' => $e->user_id]);
        } else {
            $enrollments = \App\Models\StudentCourseEnrollment::where('advanced_course_id', $exam->advanced_course_id)
                ->where('status', 'active')
                ->with('user')
                ->get();
        }
        
        // جلب المحاولات
        $attempts = ExamAttempt::where('exam_id', $exam->id)
            ->with(['user'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);
        
        // إحصائيات
        $attemptStats = [
            'total' => $attempts->total(),
            'completed' => ExamAttempt::where('exam_id', $exam->id)
                ->where('status', 'completed')->count(),
            'in_progress' => ExamAttempt::where('exam_id', $exam->id)
                ->where('status', 'in_progress')->count(),
            'average_score' => ExamAttempt::where('exam_id', $exam->id)
                ->where('status', 'completed')
                ->avg('score') ?? 0,
        ];
        
        return view('instructor.exams.show', compact('exam', 'enrollments', 'attempts', 'attemptStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdvancedExam $exam)
    {
        $instructor = Auth::user();
        
        $owns = ($exam->advancedCourse && $exam->advancedCourse->instructor_id === $instructor->id)
            || ($exam->offlineCourse && $exam->offlineCourse->instructor_id === $instructor->id);
        if (!$owns) {
            abort(403, 'غير مسموح لك بتعديل هذا الاختبار');
        }
        
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        $offlineCourses = OfflineCourse::where('instructor_id', $instructor->id)->orderBy('title')->get();
        
        $lessons = $exam->advanced_course_id
            ? \App\Models\CourseLesson::where('advanced_course_id', $exam->advanced_course_id)->orderBy('order')->get()
            : collect();
        
        $exam->load('questions');
        
        // جلب الأسئلة المتاحة
        $availableQuestions = Question::where('is_active', true)
            ->orderBy('question')
            ->get();
        
        return view('instructor.exams.edit', compact('exam', 'courses', 'offlineCourses', 'lessons', 'availableQuestions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdvancedExam $exam)
    {
        $instructor = Auth::user();
        
        $owns = ($exam->advancedCourse && $exam->advancedCourse->instructor_id === $instructor->id)
            || ($exam->offlineCourse && $exam->offlineCourse->instructor_id === $instructor->id);
        if (!$owns) {
            abort(403, 'غير مسموح لك بتعديل هذا الاختبار');
        }
        
        $validated = $request->validate([
            'advanced_course_id' => 'required_without:offline_course_id|nullable|exists:advanced_courses,id',
            'offline_course_id' => 'required_without:advanced_course_id|nullable|exists:offline_courses,id',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:0|max:' . ($request->total_marks ?? $exam->total_marks),
            'duration_minutes' => 'required|integer|min:5|max:480',
            'attempts_allowed' => 'required|integer|min:1|max:10',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_explanations' => 'boolean',
            'allow_review' => 'boolean',
            'is_active' => 'boolean',
            'sidebar_position' => 'nullable|integer|min:1|max:10',
            'show_in_sidebar' => 'boolean',
        ]);
        
        if (!empty($validated['offline_course_id'])) {
            $oc = OfflineCourse::where('id', $validated['offline_course_id'])->where('instructor_id', $instructor->id)->firstOrFail();
            $validated['advanced_course_id'] = null;
            $validated['course_lesson_id'] = null;
        } else {
            $course = AdvancedCourse::where('id', $validated['advanced_course_id'])->where('instructor_id', $instructor->id)->firstOrFail();
            $validated['offline_course_id'] = null;
            if (!empty($validated['course_lesson_id'])) {
                \App\Models\CourseLesson::where('id', $validated['course_lesson_id'])->where('advanced_course_id', $validated['advanced_course_id'])->firstOrFail();
            }
        }
        
        $validated['randomize_questions'] = $request->has('randomize_questions');
        $validated['randomize_options'] = $request->has('randomize_options');
        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['show_explanations'] = $request->has('show_explanations');
        $validated['allow_review'] = $request->has('allow_review');
        $validated['is_active'] = $request->has('is_active');
        $validated['show_in_sidebar'] = $request->has('show_in_sidebar');
        $validated['sidebar_position'] = $request->filled('sidebar_position') ? $request->sidebar_position : null;
        
        $exam->update($validated);
        
        // تحديث الأسئلة إذا تم إرسالها
        if ($request->filled('questions') && is_array($request->questions)) {
            // حذف الأسئلة القديمة
            \App\Models\ExamQuestion::where('exam_id', $exam->id)->delete();
            
            // إضافة الأسئلة الجديدة
            foreach ($request->questions as $index => $questionId) {
                \App\Models\ExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $questionId,
                    'order' => $index + 1,
                    'marks' => $request->question_marks[$questionId] ?? 1,
                ]);
            }
        }
        
        return redirect()->route('instructor.exams.show', $exam)
            ->with('success', 'تم تحديث الاختبار بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdvancedExam $exam)
    {
        $instructor = Auth::user();
        
        $owns = ($exam->advancedCourse && $exam->advancedCourse->instructor_id === $instructor->id)
            || ($exam->offlineCourse && $exam->offlineCourse->instructor_id === $instructor->id);
        if (!$owns) {
            abort(403, 'غير مسموح لك بحذف هذا الاختبار');
        }
        
        $exam->delete();
        
        return redirect()->route('instructor.exams.index')
            ->with('success', 'تم حذف الاختبار بنجاح');
    }
}

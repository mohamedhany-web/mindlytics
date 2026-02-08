<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyCourseController extends Controller
{
    /**
     * عرض الكورسات المفعلة للطالب
     */
    public function index()
    {
        $user = Auth::user();
        
        // الكورسات المفعلة للطالب
        $activeCourses = $user->activeCourses()
            ->with(['academicYear', 'academicSubject', 'teacher', 'lessons'])
            ->paginate(12);

        // إحصائيات
        $stats = [
            'total_active' => $user->activeCourses()->count(),
            'total_completed' => $user->courseEnrollments()->where('status', 'completed')->count(),
            'total_hours' => $user->activeCourses()->sum('duration_hours'),
            'avg_progress' => $this->calculateAverageProgress($user),
        ];

        return view('student.my-courses.index', compact('activeCourses', 'stats'));
    }

    /**
     * عرض تفاصيل الكورس المفعل
     */
    public function show($courseId)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()
            ->with([
                'academicYear', 
                'academicSubject', 
                'teacher', 
                'lessons.progress' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                },
                'lectures' => function($query) {
                    $query->orderBy('scheduled_at', 'desc');
                },
                'lectures.lesson',
                'lectures.instructor',
                'sections.items.item' => function($query) use ($user) {
                    // جلب تقدم الدروس والأنماط في العناصر
                    if ($query->getModel() instanceof \App\Models\CourseLesson) {
                        $query->with(['progress' => function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        }]);
                    } elseif ($query->getModel() instanceof \App\Models\LearningPattern) {
                        $query->with(['attempts' => function($q) use ($user) {
                            $q->where('user_id', $user->id)->latest();
                        }]);
                    }
                }
            ])
            ->findOrFail($courseId);

        // جلب الأقسام مع العناصر مرتبة
        $sections = $course->activeSections()
            ->with(['activeItems' => function($query) {
                $query->orderBy('order')->with('item');
            }])
            ->orderBy('order')
            ->get();
        
        // تحميل تقدم الدروس والأنماط في العناصر
        foreach ($sections as $section) {
            foreach ($section->activeItems as $curriculumItem) {
                if ($curriculumItem->item instanceof \App\Models\CourseLesson) {
                    $curriculumItem->item->load(['progress' => function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }]);
                } elseif ($curriculumItem->item instanceof \App\Models\LearningPattern) {
                    $curriculumItem->item->load(['attempts' => function($q) use ($user) {
                        $q->where('user_id', $user->id)->latest();
                    }]);
                }
            }
        }

        // حساب التقدم من المنهج
        $totalItems = 0;
        $completedItems = 0;
        
        foreach ($sections as $section) {
            foreach ($section->activeItems as $item) {
                if ($item->item instanceof \App\Models\CourseLesson) {
                    $totalItems++;
                    $progress = $item->item->progress->first();
                    if ($progress && $progress->is_completed) {
                        $completedItems++;
                    }
                } elseif ($item->item instanceof \App\Models\LearningPattern) {
                    $totalItems++;
                    $bestAttempt = $item->item->getUserBestAttempt($user->id);
                    if ($bestAttempt && $bestAttempt->status === 'completed') {
                        $completedItems++;
                    }
                }
            }
        }

        // إذا لم يكن هناك منهج، استخدم الدروس القديمة
        if ($sections->isEmpty()) {
            $totalLessons = $course->lessons->count();
            $completedLessons = $course->lessons->filter(function($lesson) {
                return $lesson->progress->isNotEmpty() && $lesson->progress->first()->is_completed;
            })->count();
            $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;
        } else {
            $progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;
            $totalLessons = $totalItems;
            $completedLessons = $completedItems;
        }

        // تجميع المحاضرات حسب الدرس (للتوافق مع الكود القديم)
        $lecturesByLesson = $course->lectures->groupBy('course_lesson_id');

        return view('student.my-courses.show', compact(
            'course', 
            'progress', 
            'totalLessons', 
            'completedLessons', 
            'lecturesByLesson',
            'sections'
        ));
    }

    /**
     * صفحة عرض المحتوى (Focus Mode)
     */
    public function learn($courseId, Request $request)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()
            ->with([
                'academicYear', 
                'academicSubject', 
                'teacher', 
                'lessons.progress' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                },
                'lectures' => function($query) {
                    $query->orderBy('scheduled_at', 'desc');
                },
                'lectures.lesson',
                'lectures.instructor',
                'sections.items.item' => function($query) use ($user) {
                    // جلب تقدم الدروس والأنماط في العناصر
                    if ($query->getModel() instanceof \App\Models\CourseLesson) {
                        $query->with(['progress' => function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        }]);
                    } elseif ($query->getModel() instanceof \App\Models\LearningPattern) {
                        $query->with(['attempts' => function($q) use ($user) {
                            $q->where('user_id', $user->id)->latest();
                        }]);
                    }
                }
            ])
            ->findOrFail($courseId);
        
        // جلب الدرس إذا تم تمرير lesson_id
        $lesson = null;
        if ($request->has('lesson')) {
            $lessonId = $request->input('lesson');
            $lesson = $course->lessons()->find($lessonId);
            
            // التحقق من أن الدرس نشط
            if ($lesson && !$lesson->is_active) {
                $lesson = null;
            }
            
            // التحقق من ترتيب الدروس (لا يمكن مشاهدة درس قبل إكمال السابق)
            if ($lesson) {
                $previousLessons = $course->lessons()
                    ->where('order', '<', $lesson->order)
                    ->where('is_active', true)
                    ->get();
                    
                foreach ($previousLessons as $prevLesson) {
                    $prevProgress = \App\Models\LessonProgress::where('user_id', $user->id)
                        ->where('course_lesson_id', $prevLesson->id)
                        ->first();
                        
                    if (!$prevProgress || !$prevProgress->is_completed) {
                        $lesson = null;
                        break;
                    }
                }
            }
        }

        // جلب الأقسام مع العناصر مرتبة
        $sections = $course->activeSections()
            ->with(['activeItems' => function($query) {
                $query->orderBy('order')->with('item');
            }])
            ->orderBy('order')
            ->get();
        
        // تحميل تقدم الدروس والأنماط في العناصر
        foreach ($sections as $section) {
            foreach ($section->activeItems as $curriculumItem) {
                if ($curriculumItem->item instanceof \App\Models\CourseLesson) {
                    $curriculumItem->item->load(['progress' => function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }]);
                } elseif ($curriculumItem->item instanceof \App\Models\LearningPattern) {
                    $curriculumItem->item->load(['attempts' => function($q) use ($user) {
                        $q->where('user_id', $user->id)->latest();
                    }]);
                }
            }
        }

        // حساب التقدم من المنهج
        $totalItems = 0;
        $completedItems = 0;
        
        foreach ($sections as $section) {
            foreach ($section->activeItems as $item) {
                if ($item->item instanceof \App\Models\CourseLesson) {
                    $totalItems++;
                    $progress = $item->item->progress->first();
                    if ($progress && $progress->is_completed) {
                        $completedItems++;
                    }
                } elseif ($item->item instanceof \App\Models\LearningPattern) {
                    $totalItems++;
                    $bestAttempt = $item->item->getUserBestAttempt($user->id);
                    if ($bestAttempt && $bestAttempt->status === 'completed') {
                        $completedItems++;
                    }
                }
            }
        }

        // إذا لم يكن هناك منهج، استخدم الدروس القديمة
        if ($sections->isEmpty()) {
            $totalLessons = $course->lessons->count();
            $completedLessons = $course->lessons->filter(function($lesson) {
                return $lesson->progress->isNotEmpty() && $lesson->progress->first()->is_completed;
            })->count();
            $progress = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;
        } else {
            $progress = $totalItems > 0 ? round(($completedItems / $totalItems) * 100, 2) : 0;
            $totalLessons = $totalItems;
            $completedLessons = $completedItems;
        }

        // تجميع المحاضرات حسب الدرس (للتوافق مع الكود القديم)
        $lecturesByLesson = $course->lectures->groupBy('course_lesson_id');

        // جلب الاختبارات المرتبة حسب الموضع في السايدبار
        $sidebarExams = \App\Models\AdvancedExam::where('advanced_course_id', $course->id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('show_in_sidebar', true)
                  ->orWhereNull('show_in_sidebar'); // للتوافق مع البيانات القديمة
            })
            ->orderByRaw('CASE WHEN sidebar_position IS NULL THEN 999 ELSE sidebar_position END ASC')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('student.my-courses.learn', compact(
            'course', 
            'progress', 
            'totalLessons', 
            'completedLessons', 
            'lecturesByLesson',
            'sections',
            'sidebarExams',
            'lesson'
        ));
    }

    /**
     * عرض الدرس في واجهة محمية
     */
    public function watchLesson($courseId, $lessonId)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);
        
        // التحقق من أن الدرس نشط
        if (!$lesson->is_active) {
            return redirect()->route('my-courses.show', $course)
                ->with('error', 'هذا الدرس غير متاح حالياً');
        }
        
        // التحقق من ترتيب الدروس (لا يمكن مشاهدة درس قبل إكمال السابق)
        $previousLessons = $course->lessons()
            ->where('order', '<', $lesson->order)
            ->where('is_active', true)
            ->get();
            
        foreach ($previousLessons as $prevLesson) {
            $prevProgress = \App\Models\LessonProgress::where('user_id', $user->id)
                ->where('course_lesson_id', $prevLesson->id)
                ->first();
                
            if (!$prevProgress || !$prevProgress->is_completed) {
                return redirect()->route('my-courses.show', $course)
                    ->with('error', 'يجب إكمال الدروس السابقة أولاً');
            }
        }
        
        return view('student.my-courses.lesson-viewer', compact('course', 'lesson'));
    }

    /**
     * حساب متوسط التقدم
     */
    private function calculateAverageProgress($user)
    {
        $enrollments = $user->courseEnrollments()->where('status', 'active')->get();
        if ($enrollments->isEmpty()) return 0;
        
        $totalProgress = $enrollments->sum('progress');
        return round($totalProgress / $enrollments->count(), 1);
    }

    /**
     * تحديث تقدم الدرس
     */
    public function updateLessonProgress(Request $request, $courseId, $lessonId)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);

        $watchTime = $request->input('watch_time', 0);
        $progressPercent = $request->input('progress_percent', 0);
        $isCompleted = $request->boolean('completed') || $progressPercent >= 90;

        // تحديث أو إنشاء تقدم الدرس
        $progress = \App\Models\LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_lesson_id' => $lessonId
            ],
            [
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? now() : null,
                'watch_time' => $watchTime
            ]
        );

        // تحديث التقدم الإجمالي للكورس
        $this->updateCourseProgress($user->id, $courseId);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التقدم بنجاح',
            'progress' => $progress,
            'course_progress' => $this->getCourseProgress($user->id, $courseId)
        ]);
    }

    /**
     * الحصول على تقدم الكورس
     */
    private function getCourseProgress($userId, $courseId)
    {
        $course = \App\Models\AdvancedCourse::findOrFail($courseId);
        $totalLessons = $course->lessons()->where('is_active', true)->count();
        
        if ($totalLessons === 0) return 0;

        $completedLessons = \App\Models\LessonProgress::where('user_id', $userId)
            ->whereIn('course_lesson_id', $course->lessons()->where('is_active', true)->pluck('id'))
            ->where('is_completed', true)
            ->count();

        return round(($completedLessons / $totalLessons) * 100, 2);
    }

    /**
     * تحديث التقدم الإجمالي للكورس
     */
    private function updateCourseProgress($userId, $courseId)
    {
        $course = \App\Models\AdvancedCourse::findOrFail($courseId);
        $totalLessons = $course->lessons()->count();
        
        if ($totalLessons > 0) {
            $completedLessons = \App\Models\LessonProgress::where('user_id', $userId)
                ->whereIn('course_lesson_id', $course->lessons()->pluck('id'))
                ->where('is_completed', true)
                ->count();

            $progressPercentage = round(($completedLessons / $totalLessons) * 100, 2);

            // تحديث التقدم في جدول التسجيلات
            \App\Models\StudentCourseEnrollment::where('user_id', $userId)
                ->where('advanced_course_id', $courseId)
                ->update(['progress' => $progressPercentage]);
        }
    }
}

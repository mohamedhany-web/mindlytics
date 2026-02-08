<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\StudentCourseEnrollment;
use App\Models\LearningPathEnrollment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LearningPathController extends Controller
{
    /**
     * عرض صفحة المسارات التعليمية
     */
    public function index()
    {
        // جلب المسارات التعليمية النشطة (AcademicYears)
        $academicYears = AcademicYear::where('is_active', true)
            ->select('*') // تأكد من جلب جميع الأعمدة بما فيها thumbnail
            ->with(['linkedCourses' => function($query) {
                $query->where('is_active', true)
                      ->with(['academicSubject', 'academicYear', 'instructor'])
                      ->withCount('lessons');
            }, 'academicSubjects' => function($query) {
                $query->where('is_active', true);
            }])
            ->withCount(['linkedCourses', 'academicSubjects'])
            ->select('*')
            ->orderBy('order')
            ->get();
        
        // تحويل AcademicYears إلى تنسيق مناسب للعرض
        $learningPaths = $academicYears->map(function($year) {
            // جمع الكورسات المرتبطة مباشرة بالمسار
            $linkedCourses = $year->linkedCourses ?? collect();
            
            // جمع الكورسات من المواد الدراسية
            $subjectCourses = collect();
            if ($year->academicSubjects && $year->academicSubjects->isNotEmpty()) {
                $subjectIds = $year->academicSubjects->pluck('id')->toArray();
                if (!empty($subjectIds)) {
                    $subjectCourses = AdvancedCourse::where('is_active', true)
                        ->whereIn('academic_subject_id', $subjectIds)
                        ->with(['academicSubject', 'academicYear', 'instructor'])
                        ->withCount('lessons')
                        ->get();
                }
            }
            
            // دمج الكورسات وإزالة التكرار
            $courses = $linkedCourses->merge($subjectCourses)->unique('id');
            
            // حساب السعر الإجمالي
            $totalPrice = $courses->sum('price');
            
            // إنشاء slug من الاسم
            $slug = Str::slug($year->name);
            
            return (object)[
                'id' => $year->id,
                'name' => $year->name,
                'description' => $year->description,
                'slug' => $slug,
                'price' => $totalPrice,
                'original_price' => null,
                'courses_count' => $courses->count(),
                'is_featured' => false,
                'is_popular' => false,
                'thumbnail' => $year->thumbnail,
                'features' => [],
                'icon' => $year->icon,
                'color' => $year->color,
                'code' => $year->code,
                'courses' => $courses,
                'academic_subjects_count' => $year->academic_subjects_count ?? 0,
                'linked_courses_count' => $year->linked_courses_count ?? 0
            ];
        });
        
        // جلب الكورسات المميزة للعرض
        $featuredCourses = AdvancedCourse::where('is_active', true)
            ->where('is_featured', true)
            ->with(['academicSubject', 'academicYear'])
            ->withCount('lessons')
            ->limit(6)
            ->get();
        
        // تحضير بيانات المسارات للبحث (Alpine.js)
        $pathsForSearch = $learningPaths->map(function($path) {
            return [
                'id' => $path->id,
                'name' => $path->name ?? '',
                'description' => $path->description ?? '',
                'slug' => $path->slug ?? '',
                'price' => (float)($path->price ?? 0),
                'original_price' => $path->original_price ? (float)$path->original_price : null,
                'courses_count' => (int)($path->courses_count ?? 0),
                'is_featured' => (bool)($path->is_featured ?? false),
                'is_popular' => (bool)($path->is_popular ?? false),
                'thumbnail' => $path->thumbnail ?? null,
                'features' => $path->features ?? [],
                'icon' => $path->icon ?? null,
                'color' => $path->color ?? null,
                'code' => $path->code ?? ''
            ];
        })->values()->all();
        
        // Debug: Log المسارات للتأكد من وجودها
        \Log::info('Academic Years Count: ' . $academicYears->count());
        \Log::info('Learning Paths Count: ' . $learningPaths->count());
        \Log::info('Paths For Search Count: ' . count($pathsForSearch));
        if ($learningPaths->count() > 0) {
            \Log::info('First Path: ' . json_encode($pathsForSearch[0] ?? []));
        }
        
        // إضافة debugging في الـ view أيضاً
        return view('public.learning-paths', compact('learningPaths', 'featuredCourses', 'pathsForSearch'))
            ->with('debug_count', $learningPaths->count());
    }

    /**
     * عرض تفاصيل مسار تعليمي
     */
    public function show($slug)
    {
        // البحث عن AcademicYear بالاسم (slug)
        $academicYear = AcademicYear::active()
            ->with(['linkedCourses' => function($query) {
                $query->where('is_active', true)
                      ->with(['academicSubject', 'academicYear', 'instructor'])
                      ->withCount('lessons');
            }, 'academicSubjects'])
            ->get()
            ->first(function($year) use ($slug) {
                return Str::slug($year->name) === $slug;
            });
        
        if (!$academicYear) {
            abort(404, 'المسار التعليمي غير موجود');
        }
        
        // جلب الكورسات من المواد الدراسية
        $subjectIds = $academicYear->academicSubjects->pluck('id')->toArray();
        $courses = collect();
        if (!empty($subjectIds)) {
            $courses = AdvancedCourse::where('is_active', true)
                ->whereIn('academic_subject_id', $subjectIds)
                ->with(['academicSubject', 'academicYear', 'instructor'])
                ->withCount('lessons')
                ->get();
        }
        
        // دمج الكورسات المرتبطة مباشرة مع الكورسات من المواد الدراسية
        $linkedCourses = $academicYear->linkedCourses()->where('is_active', true)->get();
        $allCourses = $linkedCourses->merge($courses)->unique('id');
        
        // إنشاء كائن مشابه لـ Package للتوافق مع الـ view
        $learningPath = (object)[
            'id' => $academicYear->id,
            'name' => $academicYear->name,
            'description' => $academicYear->description,
            'video_url' => $academicYear->video_url,
            'slug' => Str::slug($academicYear->name),
            'price' => $allCourses->sum('price'),
            'original_price' => null,
            'courses_count' => $allCourses->count(),
            'is_featured' => false,
            'is_popular' => false,
            'thumbnail' => $academicYear->thumbnail,
            'features' => [],
            'icon' => $academicYear->icon,
            'color' => $academicYear->color,
            'code' => $academicYear->code,
            'courses' => $allCourses,
            'academic_subjects_count' => $academicYear->academicSubjects->count()
        ];
        
        // مسارات ذات صلة
        $relatedYears = AcademicYear::active()
            ->where('id', '!=', $academicYear->id)
            ->withCount('academicSubjects')
            ->limit(3)
            ->get();
        
        $relatedPaths = $relatedYears->map(function($year) {
            $yearCourses = $year->academicSubjects->flatMap(function($subject) {
                return $subject->advancedCourses()->where('is_active', true)->get();
            })->unique('id');
            
            return (object)[
                'id' => $year->id,
                'name' => $year->name,
                'slug' => Str::slug($year->name),
                'courses_count' => $yearCourses->count(),
                'academic_subjects_count' => $year->academic_subjects_count
            ];
        });
        
        // التحقق من الاشتراك في المسار
        $isEnrolled = false;
        $enrollment = null;
        if(auth()->check()) {
            $enrollment = LearningPathEnrollment::where('user_id', auth()->id())
                ->where('academic_year_id', $academicYear->id)
                ->first();
            
            $isEnrolled = $enrollment && $enrollment->status === 'active';
        }
        
        return view('public.learning-path-show', compact('learningPath', 'relatedPaths', 'isEnrolled', 'enrollment'));
    }

    /**
     * الاشتراك في مسار تعليمي (إنشاء طلب enrollment)
     */
    public function enroll(Request $request, $slug)
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // البحث عن AcademicYear بالاسم (slug)
        $academicYear = AcademicYear::active()
            ->get()
            ->first(function($year) use ($slug) {
                return Str::slug($year->name) === $slug;
            });
        
        if (!$academicYear) {
            abort(404, 'المسار التعليمي غير موجود');
        }

        // التحقق من وجود تسجيل مسبق
        $existingEnrollment = LearningPathEnrollment::where('user_id', Auth::id())
            ->where('academic_year_id', $academicYear->id)
            ->first();

        if ($existingEnrollment) {
            if ($existingEnrollment->status === 'active') {
                return back()->with('info', 'أنت مسجل بالفعل في هذا المسار التعليمي');
            } elseif ($existingEnrollment->status === 'pending') {
                return back()->with('info', 'لديك طلب اشتراك معلق في هذا المسار، يرجى انتظار المراجعة');
            }
        }

        DB::beginTransaction();
        try {
            // إنشاء طلب enrollment جديد
            LearningPathEnrollment::create([
                'user_id' => Auth::id(),
                'academic_year_id' => $academicYear->id,
                'status' => 'pending',
                'enrolled_at' => now(),
                'progress' => 0,
            ]);

            DB::commit();

            return back()->with('success', 'تم تقديم طلب الاشتراك في المسار التعليمي بنجاح! سيتم مراجعة طلبك قريباً.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تقديم الطلب. يرجى المحاولة مرة أخرى.');
        }
    }
}

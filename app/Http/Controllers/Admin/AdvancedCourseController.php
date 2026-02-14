<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdvancedCourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.courses');
    }

    /**
     * عرض قائمة الكورسات
     */
    public function index(Request $request)
    {
        $query = AdvancedCourse::with(['instructor', 'academicYear', 'academicSubject'])
            ->withCount(['lessons', 'enrollments', 'orders']);

        // فلترة حسب لغة البرمجة
        if ($request->filled('programming_language')) {
            $query->where('programming_language', $request->programming_language);
        }

        // فلترة حسب التصنيف
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // فلترة حسب المستوى
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // فلترة حسب مجموعة المهارات (cluster)
        $selectedCluster = null;
        if ($request->filled('cluster')) {
            $query->where('academic_subject_id', $request->cluster);
            $selectedCluster = AcademicSubject::find($request->cluster);
        }

        // البحث في العنوان والوصف
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(15);

        // بيانات للفلاتر
        $programmingLanguages = AdvancedCourse::whereNotNull('programming_language')
            ->distinct()
            ->pluck('programming_language')
            ->sort()
            ->values();
        
        $categories = AdvancedCourse::whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        $instructors = User::where('role', 'instructor')->where('is_active', true)->get();

        $academicYears = AcademicYear::orderBy('order')->orderBy('name')->get(['id', 'name', 'code']);

        try {
            return view('admin.advanced-courses.index', compact(
                'courses',
                'programmingLanguages',
                'categories',
                'instructors',
                'academicYears',
                'selectedCluster'
            ));
        } catch (\Throwable $e) {
            Log::error('AdvancedCourse index view error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * عرض صفحة إنشاء كورس جديد
     */
    public function create(Request $request): View
    {
        $trackOptions = AcademicYear::with(['subjects' => function ($q) {
                $q->select('id', 'academic_year_id', 'name');
            }])
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(function (AcademicYear $year) {
                return [
                    'id' => $year->id,
                    'name' => $year->name,
                    'subjects' => $year->subjects
                        ->map(function (AcademicSubject $subject) {
                            return [
                                'id' => $subject->id,
                                'name' => $subject->name,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();

        $selectedTrack = old('academic_year_id');
        $selectedSubject = old('academic_subject_id');

        $instructors = User::whereIn('role', ['instructor', 'teacher'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $languages = AdvancedCourse::select('programming_language')->whereNotNull('programming_language')->distinct()->pluck('programming_language');
        $frameworks = AdvancedCourse::select('framework')->whereNotNull('framework')->distinct()->pluck('framework');
        $categories = AdvancedCourse::select('category')->whereNotNull('category')->distinct()->pluck('category');
        $skills = AdvancedCourse::selectRaw('DISTINCT JSON_EXTRACT(skills, "$[*]") as skill')
            ->whereNotNull('skills')
            ->pluck('skill')
            ->filter()
            ->flatMap(function ($json) {
                $decoded = json_decode($json, true);
                return is_array($decoded) ? $decoded : [$json];
            })
            ->unique()
            ->values();

        return view('admin.advanced-courses.create', compact(
            'trackOptions',
            'selectedTrack',
            'selectedSubject',
            'instructors',
            'languages',
            'frameworks',
            'categories',
            'skills'
        ));
    }

    public function getSubjectsByYear(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $subjects = AcademicSubject::where('academic_year_id', $validated['academic_year_id'])
            ->orderBy('order')
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($subjects);
    }

    /**
     * حفظ كورس جديد
     */
    public function store(Request $request)
    {
        $request->merge([
            'academic_year_id' => $request->filled('academic_year_id') ? $request->academic_year_id : null,
            'academic_subject_id' => $request->filled('academic_subject_id') ? $request->academic_subject_id : null,
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_subject_id' => 'nullable|exists:academic_subjects,id',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'objectives' => 'nullable|string',
            'instructor_id' => 'nullable|exists:users,id',
            'programming_language' => 'nullable|string|max:100',
            'framework' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'duration_hours' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0|max:59',
            'price' => 'nullable|numeric|min:0',
            'requirements' => 'nullable|string',
            'prerequisites' => 'nullable|string',
            'what_you_learn' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:10',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ], [
            'title.required' => 'عنوان الكورس مطلوب',
            'academic_year_id.exists' => 'المسار التعليمي المحدد غير موجود',
            'academic_subject_id.exists' => 'مجموعة المهارات المحددة غير موجودة',
            'instructor_id.exists' => 'المدرب المحدد غير موجود',
            'level.in' => 'مستوى الكورس غير صحيح',
            'duration_hours.numeric' => 'مدة الكورس يجب أن تكون رقم',
            'duration_hours.min' => 'مدة الكورس لا يمكن أن تكون أقل من صفر',
            'duration_minutes.integer' => 'المدة الإضافية يجب أن تكون رقم صحيح',
            'duration_minutes.max' => 'الدقائق يجب ألا تتجاوز 59 دقيقة',
            'price.numeric' => 'السعر يجب أن يكون رقم',
            'price.min' => 'السعر لا يمكن أن يكون أقل من صفر',
            'thumbnail.image' => 'يجب أن تكون صورة صحيحة',
            'thumbnail.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png أو jpg',
            'thumbnail.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
            'ends_at.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $data = array_merge(
            $request->only([
                'title',
                'academic_year_id',
                'academic_subject_id',
                'description',
                'video_url',
                'objectives',
                'instructor_id',
                'programming_language',
                'framework',
                'category',
                'level',
                'duration_hours',
                'duration_minutes',
                'price',
                'requirements',
                'prerequisites',
                'what_you_learn',
                'language',
                'starts_at',
                'ends_at',
            ]),
            [
                'is_active' => $request->boolean('is_active'),
                'is_featured' => $request->boolean('is_featured'),
                'students_count' => 0,
                'rating' => 0,
                'reviews_count' => 0,
            ]
        );
        $data['academic_year_id'] = $request->filled('academic_year_id') ? (int) $data['academic_year_id'] : null;
        $data['academic_subject_id'] = $request->filled('academic_subject_id') ? (int) $data['academic_subject_id'] : null;
        $data['instructor_id'] = isset($data['instructor_id']) && $data['instructor_id'] !== '' ? (int) $data['instructor_id'] : null;

        $data['level'] = $data['level'] ?? 'beginner';
        $data['price'] = $data['price'] ?? 0;
        $data['duration_hours'] = $data['duration_hours'] ?? 0;
        $data['duration_minutes'] = $data['duration_minutes'] ?? 0;
        $data['language'] = $data['language'] ?? 'ar';
        $data['skills'] = $request->filled('skills')
            ? array_values(array_filter($request->input('skills', [])))
            : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        try {
            AdvancedCourse::create($data);
        } catch (\Throwable $e) {
            Log::error('AdvancedCourse::store create failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        return redirect()->route('admin.advanced-courses.index')
            ->with('success', 'تم إنشاء الكورس بنجاح');
    }

    /**
     * عرض تفاصيل كورس محدد
     */
    public function show(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->load([
            'instructor',
            'lessons' => function($query) {
                $query->ordered();
            },
            'enrollments.student',
            'orders' => function($query) {
                $query->with(['user'])->orderBy('created_at', 'desc');
            }
        ]);

        // إحصائيات
        $stats = [
            'total_lessons' => $advancedCourse->lessons->count(),
            'active_lessons' => $advancedCourse->lessons->where('is_active', true)->count(),
            'total_students' => $advancedCourse->enrollments->count(),
            'active_students' => $advancedCourse->enrollments->where('status', 'active')->count(),
            'pending_orders' => $advancedCourse->orders->where('status', 'pending')->count(),
            'total_duration' => $advancedCourse->lessons->sum('duration_minutes'),
        ];

        return view('admin.advanced-courses.show', compact('advancedCourse', 'stats'));
    }

    /**
     * عرض صفحة تعديل كورس
     */
    public function edit(AdvancedCourse $advancedCourse)
    {
        $instructors = User::where('role', 'instructor')->where('is_active', true)->orderBy('name')->get();

        $academicYears = AcademicYear::orderBy('order')
            ->orderBy('name')
            ->with(['academicSubjects' => function ($query) {
                $query->orderBy('order')->orderBy('name');
            }])
            ->get();

        $academicSubjects = AcademicSubject::with('academicYear')
            ->orderBy('academic_year_id')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $languages = AdvancedCourse::whereNotNull('programming_language')
            ->distinct()
            ->orderBy('programming_language')
            ->pluck('programming_language');

        $frameworks = AdvancedCourse::whereNotNull('framework')
            ->distinct()
            ->orderBy('framework')
            ->pluck('framework');

        $categories = AdvancedCourse::whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // تحضير بيانات المسارات للـ Alpine.js
        $trackOptions = $academicYears->map(function($year) {
            return [
                'id' => $year->id,
                'name' => $year->name,
                'subjects' => $year->academicSubjects->map(function($subject) {
                    return ['id' => $subject->id, 'name' => $subject->name];
                })->values()->toArray()
            ];
        })->values()->toArray();

        // التأكد من أن trackOptions ليس فارغاً
        if (empty($trackOptions)) {
            $trackOptions = [];
        }

        // تحضير المهارات للتعديل (لتجنب أخطاء Blade في الـ view)
        $selectedSkills = old('skills');
        if ($selectedSkills === null) {
            $skills = $advancedCourse->skills;
            $selectedSkills = is_array($skills) ? $skills : (is_string($skills) ? json_decode($skills, true) : []);
        }
        $selectedSkills = is_array($selectedSkills) ? $selectedSkills : [];

        return view('admin.advanced-courses.edit', compact(
            'advancedCourse',
            'instructors',
            'academicYears',
            'academicSubjects',
            'languages',
            'frameworks',
            'categories',
            'trackOptions',
            'selectedSkills'
        ));
    }

    /**
     * تحديث بيانات كورس
     */
    public function update(Request $request, AdvancedCourse $advancedCourse)
    {
        // تحويل القيم الفارغة لـ المسار ومجموعة المهارات إلى null (اختياريان)
        $request->merge([
            'academic_year_id' => $request->filled('academic_year_id') ? $request->academic_year_id : null,
            'academic_subject_id' => $request->filled('academic_subject_id') ? $request->academic_subject_id : null,
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_subject_id' => 'nullable|exists:academic_subjects,id',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'objectives' => 'nullable|string',
            'instructor_id' => 'nullable|exists:users,id',
            'programming_language' => 'nullable|string|max:100',
            'framework' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'duration_hours' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0|max:59',
            'price' => 'nullable|numeric|min:0',
            'requirements' => 'nullable|string',
            'prerequisites' => 'nullable|string',
            'what_you_learn' => 'nullable|string',
            'skills' => 'nullable|array',
            'skills.*' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:10',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ], [
            'title.required' => 'عنوان الكورس مطلوب',
            'academic_year_id.exists' => 'المسار التعليمي المحدد غير موجود',
            'academic_subject_id.exists' => 'مجموعة المهارات المحددة غير موجودة',
            'instructor_id.exists' => 'المدرب المحدد غير موجود',
            'duration_minutes.max' => 'الدقائق يجب ألا تتجاوز 59 دقيقة',
            'thumbnail.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
            'ends_at.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',
        ]);

        $data = $request->only([
            'title',
            'academic_year_id',
            'academic_subject_id',
            'description',
            'video_url',
            'objectives',
            'instructor_id',
            'programming_language',
            'framework',
            'category',
            'level',
            'duration_hours',
            'duration_minutes',
            'price',
            'requirements',
            'prerequisites',
            'what_you_learn',
            'language',
            'starts_at',
            'ends_at',
        ]);

        $data['level'] = $data['level'] ?? 'beginner';
        $data['price'] = $data['price'] ?? 0;
        $data['duration_hours'] = $data['duration_hours'] ?? 0;
        $data['duration_minutes'] = $data['duration_minutes'] ?? 0;
        $data['language'] = $data['language'] ?? 'ar';
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['skills'] = $request->filled('skills')
            ? array_values(array_filter($request->input('skills', [])))
            : null;

        // المسار ومجموعة المهارات اختياريان في التعديل
        $data['academic_year_id'] = $request->filled('academic_year_id') ? $data['academic_year_id'] : null;
        $data['academic_subject_id'] = $request->filled('academic_subject_id') ? $data['academic_subject_id'] : null;

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $advancedCourse->update($data);

        return redirect()->route('admin.advanced-courses.index')
            ->with('success', 'تم تحديث الكورس بنجاح');
    }

    /**
     * حذف كورس
     * الحذف يتم باستعلامات مباشرة دون أحداث النموذج لتجنب خطأ 500.
     */
    public function destroy(AdvancedCourse $advancedCourse)
    {
        $courseId = $advancedCourse->id;
        $title = $advancedCourse->title;

        try {
            Log::info('بدء حذف الكورس', ['course_id' => $courseId, 'title' => $title]);

            DB::transaction(function () use ($courseId) {
                AdvancedCourse::deleteRelatedRecords($courseId);
                DB::table('advanced_courses')->where('id', $courseId)->delete();
            });

            Log::info('تم حذف الكورس بنجاح', ['course_id' => $courseId]);
            return redirect()->route('admin.advanced-courses.index')
                ->with('success', 'تم حذف الكورس بنجاح');
        } catch (\Throwable $e) {
            Log::error('حذف الكورس فشل', [
                'course_id' => $courseId,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.advanced-courses.index')
                ->with('error', 'حدث خطأ أثناء حذف الكورس. قد يكون الكورس مرتبطاً ببيانات أخرى.');
        }
    }

    /**
     * تفعيل طالب في الكورس
     */
    public function activateStudent(Request $request, AdvancedCourse $advancedCourse)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // التحقق من عدم وجود الطالب مسبقاً
        if ($advancedCourse->enrollments()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'الطالب مسجل بالفعل في هذا الكورس');
        }

        $advancedCourse->enrollments()->create([
            'user_id' => $request->user_id,
            'enrolled_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم تفعيل الطالب في الكورس بنجاح');
    }

    /**
     * عرض طلاب الكورس
     */
    public function students(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->load(['enrollments.user']);
        $availableStudents = User::where('role', 'student')
            ->whereNotIn('id', $advancedCourse->enrollments->pluck('user_id'))
            ->get();

        return view('admin.advanced-courses.students', compact('advancedCourse', 'availableStudents'));
    }


    /**
     * تغيير حالة الكورس (تفعيل/إلغاء تفعيل)
     */
    public function toggleStatus(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->update([
            'is_active' => !$advancedCourse->is_active
        ]);

        $status = $advancedCourse->is_active ? 'تم تفعيل' : 'تم إيقاف';

        return response()->json([
            'success' => true,
            'message' => $status . ' الكورس بنجاح',
            'is_active' => $advancedCourse->is_active
        ]);
    }

    /**
     * تغيير حالة الترشيح للكورس
     */
    public function toggleFeatured(AdvancedCourse $advancedCourse)
    {
        $advancedCourse->update([
            'is_featured' => !$advancedCourse->is_featured
        ]);

        $status = $advancedCourse->is_featured ? 'تم ترشيح' : 'تم إلغاء ترشيح';

        return response()->json([
            'success' => true,
            'message' => $status . ' الكورس بنجاح',
            'is_featured' => $advancedCourse->is_featured
        ]);
    }

    /**
     * عرض الطلبات الخاصة بالكورس
     */
    public function orders(AdvancedCourse $advancedCourse)
    {
        $orders = $advancedCourse->orders()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.advanced-courses.orders', compact('advancedCourse', 'orders'));
    }

    /**
     * إحصائيات الكورس
     */
    public function statistics(AdvancedCourse $advancedCourse)
    {
        $stats = [
            // إحصائيات الطلاب
            'students' => [
                'total' => $advancedCourse->enrollments->count(),
                'active' => $advancedCourse->enrollments->where('status', 'active')->count(),
                'completed' => $advancedCourse->enrollments->where('status', 'completed')->count(),
                'pending' => $advancedCourse->enrollments->where('status', 'pending')->count(),
            ],
            
            // إحصائيات الدروس
            'lessons' => [
                'total' => $advancedCourse->lessons->count(),
                'active' => $advancedCourse->lessons->where('is_active', true)->count(),
                'video' => $advancedCourse->lessons->where('type', 'video')->count(),
                'document' => $advancedCourse->lessons->where('type', 'document')->count(),
                'quiz' => $advancedCourse->lessons->where('type', 'quiz')->count(),
                'total_duration' => $advancedCourse->lessons->sum('duration_minutes'),
            ],
            
            // إحصائيات الطلبات
            'orders' => [
                'total' => $advancedCourse->orders->count(),
                'pending' => $advancedCourse->orders->where('status', 'pending')->count(),
                'approved' => $advancedCourse->orders->where('status', 'approved')->count(),
                'rejected' => $advancedCourse->orders->where('status', 'rejected')->count(),
            ],
            
            // التقدم العام
            'progress' => [
                'average' => $advancedCourse->enrollments->where('status', 'active')->avg('progress') ?? 0,
                'completion_rate' => $advancedCourse->enrollments->count() > 0 
                    ? ($advancedCourse->enrollments->where('status', 'completed')->count() / $advancedCourse->enrollments->count()) * 100 
                    : 0,
            ]
        ];

        return view('admin.advanced-courses.statistics', compact('advancedCourse', 'stats'));
    }

    /**
     * تصدير بيانات الكورس
     */
    public function export(AdvancedCourse $advancedCourse)
    {
        // يمكن تطوير هذه الوظيفة لتصدير بيانات الكورس إلى Excel أو PDF
        return response()->json([
            'message' => 'سيتم تطوير وظيفة التصدير قريباً'
        ]);
    }

    /**
     * نسخ الكورس
     */
    public function duplicate(AdvancedCourse $advancedCourse)
    {
        $newCourse = $advancedCourse->replicate();
        $newCourse->title = $advancedCourse->title . ' - نسخة';
        $newCourse->is_active = false;
        $newCourse->is_featured = false;
        $newCourse->save();

        // نسخ الدروس
        foreach ($advancedCourse->lessons as $lesson) {
            $newLesson = $lesson->replicate();
            $newLesson->advanced_course_id = $newCourse->id;
            $newLesson->save();
        }

        return redirect()->route('admin.advanced-courses.edit', $newCourse)
            ->with('success', 'تم نسخ الكورس بنجاح. يمكنك الآن تعديل البيانات حسب الحاجة.');
    }
}

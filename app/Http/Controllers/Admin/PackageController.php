<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.packages');
    }

    /**
     * عرض قائمة الباقات وإدارة الأسعار
     */
    public function index(Request $request)
    {
        // جلب الباقات
        $packagesQuery = Package::withCount('courses')
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $packagesQuery->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $packagesQuery->where('is_active', false);
            }
        }

        // البحث في الباقات
        if ($request->filled('search') && $request->tab !== 'courses') {
            $search = $request->search;
            $packagesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $packages = $packagesQuery->paginate(20, ['*'], 'packages_page');

        // جلب الكورسات لإدارة الأسعار
        $coursesQuery = AdvancedCourse::with(['instructor'])
            ->withCount('lessons')
            ->orderBy('created_at', 'desc');

        // فلترة الكورسات حسب الحالة
        if ($request->filled('course_status')) {
            if ($request->course_status === 'free') {
                $coursesQuery->where(function($q) {
                    $q->where('is_free', true)->orWhere('price', 0);
                });
            } elseif ($request->course_status === 'paid') {
                $coursesQuery->where('is_free', false)->where('price', '>', 0);
            }
        }

        // فلترة حسب المستوى
        if ($request->filled('course_level')) {
            $coursesQuery->where('level', $request->course_level);
        }

        // فلترة حسب لغة البرمجة
        if ($request->filled('course_language')) {
            $coursesQuery->where('programming_language', $request->course_language);
        }

        // فلترة حسب التصنيف
        if ($request->filled('course_category')) {
            $coursesQuery->where('category', $request->course_category);
        }

        // فلترة حسب الحالة (نشط/معطل)
        if ($request->filled('course_active')) {
            $coursesQuery->where('is_active', $request->course_active === '1');
        }

        // البحث في الكورسات
        if ($request->filled('course_search') && $request->tab === 'courses') {
            $search = $request->course_search;
            $coursesQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('programming_language', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $courses = $coursesQuery->paginate(12, ['*'], 'courses_page');

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

        // إحصائيات الباقات
        $packageStats = [
            'total' => Package::count(),
            'active' => Package::where('is_active', true)->count(),
            'inactive' => Package::where('is_active', false)->count(),
            'featured' => Package::where('is_featured', true)->count(),
        ];

        // إحصائيات الكورسات
        $courseStats = [
            'total' => AdvancedCourse::count(),
            'free' => AdvancedCourse::where(function($q) {
                $q->where('is_free', true)->orWhere('price', 0);
            })->count(),
            'paid' => AdvancedCourse::where('is_free', false)->where('price', '>', 0)->count(),
            'total_revenue' => AdvancedCourse::where('is_free', false)->sum('price'),
        ];

        return view('admin.packages.index', compact(
            'packages', 
            'courses', 
            'packageStats', 
            'courseStats',
            'programmingLanguages',
            'categories'
        ));
    }

    /**
     * عرض صفحة إنشاء باقة جديدة
     */
    public function create()
    {
        $courses = AdvancedCourse::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'price']);

        return view('admin.packages.create', compact('courses'));
    }

    /**
     * حفظ باقة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packages,slug',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration_days' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:advanced_courses,id',
        ]);

        // إنشاء slug إذا لم يتم توفيره
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // رفع الصورة
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('packages', 'public');
        }

        // إنشاء الباقة
        $package = Package::create($validated);

        // ربط الكورسات بالباقة
        if (isset($validated['courses'])) {
            $coursesData = [];
            foreach ($validated['courses'] as $index => $courseId) {
                $coursesData[$courseId] = ['order' => $index];
            }
            $package->courses()->sync($coursesData);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم إنشاء الباقة بنجاح');
    }

    /**
     * عرض تفاصيل باقة
     */
    public function show(Package $package)
    {
        $package->load('courses');
        return view('admin.packages.show', compact('package'));
    }

    /**
     * عرض صفحة تعديل باقة
     */
    public function edit(Package $package)
    {
        $package->load('courses');
        $courses = AdvancedCourse::where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'price']);

        return view('admin.packages.edit', compact('package', 'courses'));
    }

    /**
     * تحديث باقة
     */
    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:packages,slug,' . $package->id,
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration_days' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'courses' => 'required|array|min:1',
            'courses.*' => 'exists:advanced_courses,id',
        ]);

        // إنشاء slug إذا لم يتم توفيره
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // رفع الصورة
        if ($request->hasFile('thumbnail')) {
            // حذف الصورة القديمة
            if ($package->thumbnail) {
                \Storage::disk('public')->delete($package->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('packages', 'public');
        }

        // تحديث الباقة
        $package->update($validated);

        // تحديث الكورسات المرتبطة
        if (isset($validated['courses'])) {
            $coursesData = [];
            foreach ($validated['courses'] as $index => $courseId) {
                $coursesData[$courseId] = ['order' => $index];
            }
            $package->courses()->sync($coursesData);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم تحديث الباقة بنجاح');
    }

    /**
     * حذف باقة
     */
    public function destroy(Package $package)
    {
        // حذف الصورة
        if ($package->thumbnail) {
            \Storage::disk('public')->delete($package->thumbnail);
        }

        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم حذف الباقة بنجاح');
    }

    /**
     * تحديث سعر كورس (للتوافق مع الكود القديم)
     */
    public function updatePrice(Request $request, AdvancedCourse $course)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'is_free' => 'boolean',
        ]);

        $course->update([
            'price' => $validated['price'],
            'is_free' => $validated['is_free'] ?? ($validated['price'] == 0),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث السعر بنجاح',
                'course' => $course->fresh()
            ]);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم تحديث السعر بنجاح');
    }

    /**
     * تحديث أسعار متعددة
     */
    public function updateBulkPrices(Request $request)
    {
        $validated = $request->validate([
            'courses' => 'required|array',
            'courses.*.id' => 'required|exists:advanced_courses,id',
            'courses.*.price' => 'required|numeric|min:0',
            'courses.*.is_free' => 'boolean',
        ]);

        foreach ($validated['courses'] as $courseData) {
            AdvancedCourse::where('id', $courseData['id'])->update([
                'price' => $courseData['price'],
                'is_free' => $courseData['is_free'] ?? ($courseData['price'] == 0),
            ]);
        }

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم تحديث الأسعار بنجاح');
    }
}

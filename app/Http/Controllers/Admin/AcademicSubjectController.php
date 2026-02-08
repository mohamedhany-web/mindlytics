<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AcademicSubjectController extends Controller
{
    public function index(Request $request)
    {
        $trackId = $request->query('track');

        $subjectsQuery = AcademicSubject::with(['academicYear'])
            ->when($trackId, fn ($query) => $query->where('academic_year_id', $trackId))
            ->orderBy('academic_year_id')
            ->orderBy('order')
            ->orderBy('name');

        $subjects = $subjectsQuery->get();

        $allCourses = AdvancedCourse::where('is_active', true)
            ->select([
                'id',
                'title',
                'description',
                'category',
                'programming_language',
                'framework',
                'level',
                'duration_hours',
                'duration_minutes',
                'price',
                'is_free',
                'rating',
                'skills',
                'created_at',
            ])
            ->get();

        $clusters = $subjects->map(function (AcademicSubject $subject) use ($allCourses) {
            return $this->hydrateCluster($subject, $allCourses);
        });

        $summary = [
            'total_clusters' => $clusters->count(),
            'active_clusters' => $clusters->where('is_active', true)->count(),
            'courses' => $clusters->sum(fn ($cluster) => optional($cluster->cluster_metrics)['courses_count'] ?? 0),
            'languages' => $clusters->flatMap(fn ($cluster) => optional($cluster->cluster_metrics)['languages'] ?? [])->filter()->unique()->values(),
            'frameworks' => $clusters->flatMap(fn ($cluster) => optional($cluster->cluster_metrics)['frameworks'] ?? [])->filter()->unique()->values(),
            'levels' => $clusters->flatMap(fn ($cluster) => optional($cluster->cluster_metrics)['levels'] ?? [])->filter()->unique()->values(),
        ];

        $currentTrack = $trackId ? AcademicYear::find($trackId) : null;

        $tracks = AcademicYear::orderBy('order')->orderBy('name')->pluck('name', 'id');

        return view('admin.academic-subjects.index', compact('clusters', 'summary', 'currentTrack', 'tracks'));
    }

    public function create(Request $request)
    {
        $academicYears = AcademicYear::orderBy('order')->orderBy('name')->get();
        $selectedTrack = $request->query('track');

        $skills = AdvancedCourse::where('is_active', true)
            ->pluck('skills')
            ->filter()
            ->flatMap(function ($skills) {
                return collect(is_array($skills) ? $skills : json_decode($skills, true) ?? []);
            })
            ->filter()
            ->unique()
            ->values();

        $languages = AdvancedCourse::whereNotNull('programming_language')
            ->distinct()
            ->orderBy('programming_language')
            ->pluck('programming_language');

        $frameworks = AdvancedCourse::whereNotNull('framework')
            ->distinct()
            ->orderBy('framework')
            ->pluck('framework');

        return view('admin.academic-subjects.create', compact('academicYears', 'skills', 'languages', 'frameworks', 'selectedTrack'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('academic_subjects')->where(function ($query) use ($request) {
                    return $query->where('academic_year_id', $request->academic_year_id);
                })
            ],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer|min:0',
            'skills' => 'nullable|array',
            'skills.*' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ], [
            'academic_year_id.required' => 'المسار التعليمي مطلوب',
            'academic_year_id.exists' => 'المسار المحدد غير موجود',
            'name.required' => 'اسم مجموعة المهارات مطلوب',
            'name.max' => 'اسم المجموعة لا يجب أن يتجاوز 255 حرف',
            'code.required' => 'رمز المجموعة مطلوب',
            'code.unique' => 'رمز المجموعة موجود مسبقاً في هذا المسار',
            'code.max' => 'رمز المجموعة لا يجب أن يتجاوز 100 حرف',
        ]);

        AcademicSubject::create([
            'academic_year_id' => $validated['academic_year_id'],
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'fas fa-layer-group',
            'color' => $validated['color'] ?? '#0ea5e9',
            'order' => $validated['order'] ?? 0,
            'skills' => !empty($validated['skills']) ? array_values(array_filter($validated['skills'])) : null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.academic-subjects.index', ['track' => $validated['academic_year_id']])
            ->with('success', 'تم إضافة مجموعة المهارات بنجاح');
    }

    public function show(AcademicSubject $academicSubject)
    {
        $academicSubject->load(['academicYear']);
        
        // إضافة advancedCourses كـ collection فارغة لأن العلاقة لم تعد موجودة
        $academicSubject->setRelation('advancedCourses', collect());

        return view('admin.academic-subjects.show', compact('academicSubject'));
    }

    public function edit(AcademicSubject $academicSubject)
    {
        $academicYears = AcademicYear::where('is_active', true)->orderBy('order')->get();
        return view('admin.academic-subjects.edit', compact('academicSubject', 'academicYears'));
    }

    public function update(Request $request, AcademicSubject $academicSubject)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('academic_subjects')->where(function ($query) use ($request) {
                    return $query->where('academic_year_id', $request->academic_year_id);
                })->ignore($academicSubject->id)
            ],
            'description' => 'nullable|string',
            'icon' => 'required|string|max:100',
            'color' => 'required|string|max:7',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'academic_year_id.required' => 'المسار التعليمي مطلوب',
            'academic_year_id.exists' => 'المسار المحدد غير موجود',
            'name.required' => 'اسم المجموعة المهارية مطلوب',
            'name.max' => 'اسم المجموعة لا يجب أن يتجاوز 255 حرف',
            'code.required' => 'رمز المجموعة مطلوب',
            'code.unique' => 'رمز المجموعة موجود مسبقاً في هذا المسار',
            'code.max' => 'رمز المجموعة لا يجب أن يتجاوز 100 حرف',
            'icon.required' => 'أيقونة المجموعة مطلوبة',
            'color.required' => 'لون المجموعة مطلوب',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? 0;

        $academicSubject->update($data);

        return redirect()->route('admin.academic-subjects.index')
            ->with('success', 'تم تحديث المجموعة المهارية بنجاح');
    }

    public function destroy(AcademicSubject $academicSubject)
    {
        // التحقق من وجود كورسات (العلاقة لم تعد موجودة، لذا دائماً false)
        // يمكن إزالة هذا الشرط أو تركه للتوافق
        if (false) {
            return redirect()->route('admin.academic-subjects.index')
                ->with('error', 'لا يمكن حذف المجموعة المهارية لأنها تحتوي على كورسات');
        }

        $academicSubject->delete();

        return redirect()->route('admin.academic-subjects.index')
            ->with('success', 'تم حذف المجموعة المهارية بنجاح');
    }

    public function toggleStatus(AcademicSubject $academicSubject)
    {
        $academicSubject->update([
            'is_active' => !$academicSubject->is_active
        ]);

        $status = $academicSubject->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';

        return response()->json([
            'success' => true,
            'message' => $status . ' المجموعة المهارية بنجاح',
            'is_active' => $academicSubject->is_active
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:academic_subjects,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->items as $item) {
            AcademicSubject::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة ترتيب المجموعات المهارية بنجاح'
        ]);
    }

    private function hydrateCluster(AcademicSubject $subject, Collection $courses): AcademicSubject
    {
        $track = $subject->academicYear;

        $matchedCourses = $this->filterCourses($courses, [
            optional($track)->code,
            optional($track)->name,
            $subject->code,
            $subject->name,
            $subject->description,
        ]);

        if ($matchedCourses->isEmpty()) {
            $matchedCourses = $courses;
        }

        $languages = $matchedCourses->pluck('programming_language')->filter()->unique()->values();
        $frameworks = $matchedCourses->pluck('framework')->filter()->unique()->values();
        $levels = $matchedCourses->pluck('level')->filter()->unique()->values();
        $minutes = $matchedCourses->sum(function ($course) {
            return ((int) ($course->duration_hours ?? 0) * 60) + (int) ($course->duration_minutes ?? 0);
        });
        $avgMinutes = $matchedCourses->count() > 0 ? (int) round($minutes / $matchedCourses->count()) : 0;

        $subject->setAttribute('cluster_metrics', [
            'courses_count' => $matchedCourses->count(),
            'languages' => $languages->take(8),
            'frameworks' => $frameworks->take(8),
            'levels' => $levels,
            'avg_duration' => $this->formatDurationMinutes($avgMinutes),
            'avg_rating' => $matchedCourses->count() > 0 ? round((float) ($matchedCourses->avg('rating') ?? 0), 1) : null,
        ]);

        $subject->setRelation('preview_courses', $matchedCourses->sortByDesc('created_at')->take(3));

        return $subject;
    }

    private function filterCourses(Collection $courses, array $identifiers): Collection
    {
        $needles = collect($identifiers)
            ->filter()
            ->map(fn ($value) => Str::of($value)->lower()->replace(['-', '_'], ' ')->squish())
            ->filter(fn ($value) => $value->isNotEmpty());

        if ($needles->isEmpty()) {
            return collect();
        }

        return $courses->filter(function (AdvancedCourse $course) use ($needles) {
            $fields = collect([
                $course->category,
                $course->programming_language,
                $course->framework,
                $course->level,
                $course->title,
                $course->description,
            ])->merge((array) ($course->skills ?? []));

            return $fields->contains(function ($field) use ($needles) {
                if (empty($field)) {
                    return false;
                }

                $value = Str::of($field)->lower()->replace(['-', '_'], ' ')->squish();

                foreach ($needles as $needle) {
                    if ($needle->isNotEmpty() && Str::contains($value, $needle)) {
                        return true;
                    }
                }

                return false;
            });
        })->values();
    }

    private function formatDurationMinutes(int $minutes): ?string
    {
        if ($minutes <= 0) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        if ($hours === 0) {
            return $remaining . ' د';
        }

        if ($remaining === 0) {
            return $hours . ' س';
        }

        return $hours . ' س ' . $remaining . ' د';
    }
}
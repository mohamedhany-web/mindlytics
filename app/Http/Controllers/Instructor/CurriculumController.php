<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\AdvancedExam;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\CourseLesson;
use App\Models\Lecture;
use App\Models\Assignment;
use App\Services\ScholarshipCurriculumVisibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CurriculumController extends Controller
{
    public function __construct(
        private ScholarshipCurriculumVisibilityService $scholarshipVisibility
    ) {
    }

    /**
     * عرض صفحة بناء المنهج للكورس
     */
    public function index(AdvancedCourse $course)
    {
        $instructor = Auth::user();
        
        // التحقق من أن الكورس يخص هذا المدرب
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا الكورس');
        }
        
        // جلب كل الأقسام مع العناصر ثم ربط كل قسم بأبنائه لعرض الشجرة
        $allSections = $course->sections()
            ->with([
                'visibleStudents:id,name,email',
                'visibleGroups:id,name',
                'items' => function ($query) {
                    $query->orderBy('order')->with(['visibleStudents:id,name,email', 'visibleGroups:id,name']);
                },
            ])
            ->orderBy('order')
            ->get();
        foreach ($allSections as $section) {
            $section->setRelation('children', $allSections->where('parent_id', $section->id)->values());
        }
        $sections = $allSections->whereNull('parent_id')->values();
        $sectionsFlatForSelect = $this->flattenSectionsForSelect($sections);

        $isScholarshipCurriculum = $this->scholarshipVisibility->isScholarshipCourse($course);
        $scholarshipStudents = $isScholarshipCurriculum
            ? $this->scholarshipVisibility->selectableStudents($course)
            : collect();
        $scholarshipGroups = $isScholarshipCurriculum
            ? $this->scholarshipVisibility->selectableGroups($course)
            : collect();

        $sectionVisibilityMap = [];
        $itemVisibilityMap = [];
        foreach ($allSections as $section) {
            $sectionVisibilityMap[$section->id] = [
                'scope' => $section->visibility_scope ?? 'all',
                'student_ids' => $section->visibleStudents->pluck('id')->values()->all(),
                'group_ids' => $section->visibleGroups->pluck('id')->values()->all(),
            ];
            foreach ($section->items as $item) {
                $itemVisibilityMap[$item->id] = [
                    'scope' => $item->visibility_scope ?? 'all',
                    'student_ids' => $item->visibleStudents->pluck('id')->values()->all(),
                    'group_ids' => $item->visibleGroups->pluck('id')->values()->all(),
                ];
            }
        }
        
        // جلب العناصر المتاحة (محاضرات، واجبات، امتحانات، أنماط) — تم إلغاء الدروس
        $availableLectures = $course->lectures()
            ->whereDoesntHave('curriculumItems')
            ->orderBy('scheduled_at', 'desc')
            ->get();
        
        $availableAssignments = Assignment::where(function($q) use ($course) {
                $q->where('advanced_course_id', $course->id)
                  ->orWhere('course_id', $course->id);
            })
            ->whereDoesntHave('curriculumItems')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $availableExams = \App\Models\AdvancedExam::where('advanced_course_id', $course->id)
            ->whereDoesntHave('curriculumItems')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $availableLearningPatterns = \App\Models\LearningPattern::where('advanced_course_id', $course->id)
            ->whereDoesntHave('curriculumItems')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
        
        return view('instructor.curriculum.index', compact(
            'course',
            'sections',
            'sectionsFlatForSelect',
            'availableLectures',
            'availableAssignments',
            'availableExams',
            'availableLearningPatterns',
            'isScholarshipCurriculum',
            'scholarshipStudents',
            'scholarshipGroups',
            'sectionVisibilityMap',
            'itemVisibilityMap'
        ));
    }

    /**
     * إنشاء قسم جديد
     */
    public function storeSection(Request $request, AdvancedCourse $course)
    {
        $instructor = Auth::user();
        
        // التحقق من أن الكورس يخص هذا المدرب
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا الكورس');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:course_sections,id',
            'visibility_scope' => 'nullable|string|in:all,selected',
            'visible_student_ids' => 'nullable|array',
            'visible_student_ids.*' => 'integer|exists:users,id',
        ], [
            'title.required' => 'عنوان القسم مطلوب',
        ]);
        
        $parentId = $validated['parent_id'] ?? null;
        if ($parentId) {
            $parent = CourseSection::where('id', $parentId)->where('advanced_course_id', $course->id)->firstOrFail();
            $lastOrder = CourseSection::where('parent_id', $parentId)->max('order') ?? 0;
        } else {
            $lastOrder = $course->sections()->whereNull('parent_id')->max('order') ?? 0;
        }
        
        $section = CourseSection::create([
            'advanced_course_id' => $course->id,
            'parent_id' => $parentId,
            'title' => $validated['title'],
            'description' => $parentId ? null : ($validated['description'] ?? null),
            'order' => $lastOrder + 1,
            'is_active' => true,
            'visibility_scope' => 'all',
        ]);

        $this->scholarshipVisibility->syncSectionVisibility(
            $section,
            $course,
            $validated['visibility_scope'] ?? 'all',
            $validated['visible_student_ids'] ?? []
        );
        
        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء القسم بنجاح',
            'section' => $section->fresh(['visibleStudents:id']),
        ]);
    }

    /**
     * تحديث قسم
     */
    public function updateSection(Request $request, CourseSection $section)
    {
        $instructor = Auth::user();
        
        // التحقق من أن القسم يخص المدرب
        if ($section->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذا القسم');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unlock_rule' => 'nullable|string|in:always,previous_percent,previous_all_items',
            'unlock_percent' => 'nullable|integer|min:0|max:100',
            'visibility_scope' => 'nullable|string|in:all,selected',
            'visible_student_ids' => 'nullable|array',
            'visible_student_ids.*' => 'integer|exists:users,id',
        ]);
        if ($section->parent_id) {
            $validated['description'] = null;
        }
        $validated['unlock_rule'] = $validated['unlock_rule'] ?? 'previous_all_items';
        if (($validated['unlock_rule'] ?? '') !== 'previous_percent') {
            $validated['unlock_percent'] = null;
        }

        $visibilityScope = $validated['visibility_scope'] ?? null;
        $visibleStudentIds = $validated['visible_student_ids'] ?? null;
        unset($validated['visibility_scope'], $validated['visible_student_ids']);

        $section->update($validated);

        if ($visibilityScope !== null) {
            $this->scholarshipVisibility->syncSectionVisibility(
                $section,
                $section->course,
                $visibilityScope,
                $visibleStudentIds ?? []
            );
        }
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث القسم بنجاح',
            'section' => $section->fresh(['visibleStudents:id']),
        ]);
    }

    /**
     * تحديث ظهور قسم لطلبة المنحة
     */
    public function updateSectionVisibility(Request $request, CourseSection $section)
    {
        $instructor = Auth::user();
        if ($section->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذا القسم');
        }

        $validated = $request->validate([
            'visibility_scope' => 'required|string|in:all,selected,groups',
            'visible_student_ids' => 'nullable|array',
            'visible_student_ids.*' => 'integer|exists:users,id',
            'visible_group_ids' => 'nullable|array',
            'visible_group_ids.*' => 'integer|exists:scholarship_groups,id',
        ]);

        $this->scholarshipVisibility->syncSectionVisibility(
            $section,
            $section->course,
            $validated['visibility_scope'],
            $validated['visible_student_ids'] ?? [],
            $validated['visible_group_ids'] ?? []
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث وصول القسم بنجاح',
            'section' => $section->fresh(['visibleStudents:id', 'visibleGroups:id']),
        ]);
    }

    /**
     * تحديث ظهور عنصر المنهج لطلبة المنحة
     */
    public function updateItemVisibility(Request $request, CurriculumItem $item)
    {
        $instructor = Auth::user();
        $section = $item->section;
        if (! $section || $section->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذا العنصر');
        }

        $validated = $request->validate([
            'visibility_scope' => 'required|string|in:all,selected,groups',
            'visible_student_ids' => 'nullable|array',
            'visible_student_ids.*' => 'integer|exists:users,id',
            'visible_group_ids' => 'nullable|array',
            'visible_group_ids.*' => 'integer|exists:scholarship_groups,id',
        ]);

        $this->scholarshipVisibility->syncItemVisibility(
            $item,
            $section->course,
            $validated['visibility_scope'],
            $validated['visible_student_ids'] ?? [],
            $validated['visible_group_ids'] ?? []
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث ظهور العنصر بنجاح',
            'item' => $item->fresh(['visibleStudents:id', 'visibleGroups:id']),
        ]);
    }

    /**
     * حذف قسم
     */
    public function destroySection(CourseSection $section)
    {
        $instructor = Auth::user();
        
        // التحقق من أن القسم يخص المدرب
        if ($section->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بحذف هذا القسم');
        }
        
        $section->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'تم حذف القسم بنجاح',
        ]);
    }

    /**
     * إضافة عنصر للمنهج
     */
    public function addItem(Request $request, CourseSection $section)
    {
        $instructor = Auth::user();
        
        // التحقق من أن القسم يخص المدرب
        if ($section->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بإضافة عناصر لهذا القسم');
        }
        
        $validated = $request->validate([
            'item_type' => 'required|string|in:App\Models\Lecture,App\Models\Assignment,App\Models\AdvancedExam',
            'item_id' => 'required|integer',
        ]);
        
        // التحقق من وجود العنصر
        $itemModel = $validated['item_type'];
        $item = $itemModel::findOrFail($validated['item_id']);
        
        // التحقق من أن العنصر يخص نفس الكورس
        $courseId = null;
        if ($item instanceof Lecture) {
            $courseId = $item->course_id;
        } elseif ($item instanceof Assignment) {
            $courseId = $item->advanced_course_id ?? $item->course_id;
        } elseif ($item instanceof \App\Models\AdvancedExam) {
            $courseId = $item->advanced_course_id;
        }
        
        if ($courseId !== $section->advanced_course_id) {
            abort(403, 'العنصر لا يخص هذا الكورس');
        }
        
        // التحقق من عدم إضافة العنصر مسبقاً
        $exists = CurriculumItem::where('course_section_id', $section->id)
            ->where('item_type', $validated['item_type'])
            ->where('item_id', $validated['item_id'])
            ->exists();
        
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'هذا العنصر موجود بالفعل في القسم',
            ], 422);
        }
        
        // الحصول على آخر ترتيب
        $lastOrder = $section->items()->max('order') ?? 0;
        
        $curriculumItem = CurriculumItem::create([
            'course_section_id' => $section->id,
            'item_type' => $validated['item_type'],
            'item_id' => $validated['item_id'],
            'order' => $lastOrder + 1,
            'is_active' => true,
        ]);
        
        $curriculumItem->load('item');
        
        return response()->json([
            'success' => true,
            'message' => 'تم إضافة العنصر بنجاح',
            'item' => $curriculumItem,
        ]);
    }

    /**
     * إنشاء امتحان جديد من صفحة المنهج وإضافته للقسم مباشرة
     */
    public function storeExamFromCurriculum(Request $request, AdvancedCourse $course)
    {
        $instructor = Auth::user();
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بإضافة امتحان لهذا الكورس');
        }

        $validated = $request->validate([
            'section_id' => 'required|exists:course_sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'total_marks' => 'required|numeric|min:1',
            'passing_marks' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'attempts_allowed' => 'required|integer|min:1|max:10',
            'course_lesson_id' => 'nullable|exists:course_lessons,id',
        ], [
            'section_id.required' => 'القسم مطلوب',
            'title.required' => 'عنوان الاختبار مطلوب',
        ]);

        $section = CourseSection::where('id', $validated['section_id'])
            ->where('advanced_course_id', $course->id)
            ->firstOrFail();

        if ($validated['passing_marks'] > $validated['total_marks']) {
            return response()->json([
                'success' => false,
                'message' => 'درجة النجاح يجب ألا تتجاوز الدرجة الكلية',
            ], 422);
        }

        if (!empty($validated['course_lesson_id'])) {
            CourseLesson::where('id', $validated['course_lesson_id'])
                ->where('advanced_course_id', $course->id)
                ->firstOrFail();
        }

        $exam = AdvancedExam::create([
            'advanced_course_id' => $course->id,
            'course_lesson_id' => $validated['course_lesson_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'total_marks' => $validated['total_marks'],
            'passing_marks' => $validated['passing_marks'],
            'duration_minutes' => $validated['duration_minutes'],
            'attempts_allowed' => $validated['attempts_allowed'],
            'created_by' => $instructor->id,
            'randomize_questions' => false,
            'randomize_options' => false,
            'show_results_immediately' => true,
            'show_correct_answers' => true,
            'show_explanations' => false,
            'allow_review' => true,
            'is_active' => true,
            'is_published' => true,
            'show_in_sidebar' => true,
        ]);

        $lastOrder = $section->items()->max('order') ?? 0;
        CurriculumItem::create([
            'course_section_id' => $section->id,
            'item_type' => AdvancedExam::class,
            'item_id' => $exam->id,
            'order' => $lastOrder + 1,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الامتحان وإضافته للمنهج بنجاح',
            'exam_id' => $exam->id,
            'redirect' => route('instructor.exams.questions.manage', $exam),
        ]);
    }

    /**
     * إنشاء واجب جديد من صفحة المنهج وإضافته للقسم مباشرة (بدون فتح صفحة اختيار الكورس)
     */
    public function storeAssignmentFromCurriculum(Request $request, AdvancedCourse $course)
    {
        $instructor = Auth::user();
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بإضافة واجب لهذا الكورس');
        }

        $validated = $request->validate([
            'section_id' => 'required|exists:course_sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
            'max_score' => 'required|integer|min:1|max:1000',
            'allow_late_submission' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ], [
            'section_id.required' => 'القسم مطلوب',
            'title.required' => 'عنوان الواجب مطلوب',
        ]);

        $section = CourseSection::where('id', $validated['section_id'])
            ->where('advanced_course_id', $course->id)
            ->firstOrFail();

        $assignment = Assignment::create([
            'advanced_course_id' => $course->id,
            'teacher_id' => $instructor->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'max_score' => $validated['max_score'],
            'allow_late_submission' => $request->boolean('allow_late_submission'),
            'status' => $validated['status'],
        ]);

        $lastOrder = $section->items()->max('order') ?? 0;
        CurriculumItem::create([
            'course_section_id' => $section->id,
            'item_type' => Assignment::class,
            'item_id' => $assignment->id,
            'order' => $lastOrder + 1,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الواجب وإضافته للمنهج بنجاح',
        ]);
    }

    /**
     * حذف عنصر من المنهج
     */
    public function removeItem(CurriculumItem $item)
    {
        $instructor = Auth::user();
        
        // التحقق من أن العنصر يخص المدرب
        if ($item->section->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بحذف هذا العنصر');
        }
        
        $item->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'تم حذف العنصر بنجاح',
        ]);
    }

    /**
     * تحديث ترتيب الأقسام
     */
    public function updateSectionsOrder(Request $request, AdvancedCourse $course)
    {
        $instructor = Auth::user();
        
        // التحقق من أن الكورس يخص هذا المدرب
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذا الكورس');
        }
        
        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:course_sections,id',
            'sections.*.order' => 'required|integer',
            'sections.*.parent_id' => 'nullable|exists:course_sections,id',
        ]);
        
        $sectionIds = collect($validated['sections'])->pluck('id')->all();
        $courseSectionIds = $course->sections()->pluck('id')->all();
        if (count(array_intersect($sectionIds, $courseSectionIds)) !== count($sectionIds)) {
            abort(403, 'بعض الأقسام لا تخص هذا الكورس');
        }
        
        DB::transaction(function() use ($validated, $course) {
            foreach ($validated['sections'] as $sectionData) {
                $updates = ['order' => $sectionData['order']];
                if (array_key_exists('parent_id', $sectionData)) {
                    $parentId = $sectionData['parent_id'];
                    if ($parentId && !in_array($parentId, $course->sections()->pluck('id')->all())) {
                        continue;
                    }
                    if ((int) $sectionData['id'] === (int) $parentId) {
                        continue;
                    }
                    $updates['parent_id'] = $parentId;
                }
                CourseSection::where('id', $sectionData['id'])
                    ->where('advanced_course_id', $course->id)
                    ->update($updates);
            }
        });
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الترتيب بنجاح',
        ]);
    }

    /**
     * تحديث ترتيب العناصر داخل القسم
     */
    public function updateItemsOrder(Request $request, CourseSection $section)
    {
        $instructor = Auth::user();
        
        // التحقق من أن القسم يخص المدرب
        if ($section->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذا القسم');
        }
        
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:curriculum_items,id',
            'items.*.order' => 'required|integer',
        ]);
        
        DB::transaction(function() use ($validated) {
            foreach ($validated['items'] as $itemData) {
                CurriculumItem::where('id', $itemData['id'])
                    ->update(['order' => $itemData['order']]);
            }
        });
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الترتيب بنجاح',
        ]);
    }

    /**
     * نقل عنصر منهج إلى قسم آخر (سحب وإفلات بين الأقسام)
     */
    public function moveItem(Request $request, CurriculumItem $item)
    {
        $instructor = Auth::user();
        $section = $item->section;
        if (!$section || $section->course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بنقل هذا العنصر');
        }
        
        $validated = $request->validate([
            'section_id' => 'required|exists:course_sections,id',
            'order' => 'required|integer|min:0',
        ]);
        
        $targetSection = CourseSection::where('id', $validated['section_id'])
            ->where('advanced_course_id', $section->advanced_course_id)
            ->firstOrFail();
        
        $item->update([
            'course_section_id' => $targetSection->id,
            'order' => $validated['order'],
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم نقل العنصر بنجاح',
        ]);
    }

    /**
     * تسطيح شجرة الأقسام مع العمق لعرضها في قائمة الاختيار (محاضرة/امتحان/واجب)
     */
    private function flattenSectionsForSelect($sections, int $depth = 0): \Illuminate\Support\Collection
    {
        $result = collect();
        foreach ($sections as $section) {
            $result->push((object)['section' => $section, 'depth' => $depth]);
            $result = $result->merge($this->flattenSectionsForSelect($section->children, $depth + 1));
        }
        return $result;
    }
}

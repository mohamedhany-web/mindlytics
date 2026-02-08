<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionCategory;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;
use Illuminate\Http\Request;

class QuestionCategoryController extends Controller
{
    /**
     * عرض التصنيفات
     */
    public function index()
    {
        $categories = QuestionCategory::with([
                                        'academicYear',
                                        'academicSubject',
                                        'parent',
                                        'children' => function ($q) {
                                            $q->withCount('questions')->orderBy('order');
                                        }
                                    ])
                                    ->withCount(['questions'])
                                    ->main()
                                    ->orderBy('order')
                                    ->get();

        try {
            $stats = [
                'total' => QuestionCategory::count(),
                'active' => QuestionCategory::active()->count(),
                'main' => QuestionCategory::main()->count(),
                'total_questions' => \App\Models\Question::count(),
            ];
        } catch (\Throwable $e) {
            \Log::warning('QuestionCategoryController@index stats: ' . $e->getMessage());
            $stats = ['total' => 0, 'active' => 0, 'main' => 0, 'total_questions' => 0];
        }

        return view('admin.question-categories.index', compact('categories', 'stats'));
    }

    /**
     * عرض صفحة إضافة تصنيف
     */
    public function create()
    {
        $academicYears = AcademicYear::active()->orderBy('order')->get();
        $academicSubjects = AcademicSubject::active()->orderBy('name')->get();
        $parentCategories = QuestionCategory::active()->main()->orderBy('name')->get();

        return view('admin.question-categories.create', compact(
            'academicYears', 'academicSubjects', 'parentCategories'
        ));
    }

    /**
     * حفظ تصنيف جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_subject_id' => 'required|exists:academic_subjects,id',
            'parent_id' => 'nullable|exists:question_categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'اسم التصنيف مطلوب',
            'academic_year_id.required' => 'السنة الدراسية مطلوبة',
            'academic_subject_id.required' => 'المادة الدراسية مطلوبة',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        // تحديد الترتيب إذا لم يتم تحديده
        if (!isset($data['order'])) {
            $data['order'] = QuestionCategory::where('parent_id', $data['parent_id'])->max('order') + 1;
        }

        QuestionCategory::create($data);

        return redirect()->route('admin.question-categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    /**
     * عرض تفاصيل التصنيف
     */
    public function show(QuestionCategory $questionCategory)
    {
        $questionCategory->load([
            'academicYear',
            'academicSubject', 
            'parent',
            'children.questions',
            'questions' => function($query) {
                $query->with(['category'])->orderBy('created_at', 'desc');
            }
        ]);

        // إحصائيات التصنيف
        $stats = [
            'total_questions' => $questionCategory->total_questions_count,
            'direct_questions' => $questionCategory->questions->count(),
            'subcategories' => $questionCategory->children->count(),
            'by_type' => $questionCategory->questions->groupBy('type')->map->count(),
            'by_difficulty' => $questionCategory->questions->groupBy('difficulty_level')->map->count(),
        ];

        return view('admin.question-categories.show', compact('questionCategory', 'stats'));
    }

    /**
     * عرض صفحة تعديل التصنيف
     */
    public function edit(QuestionCategory $questionCategory)
    {
        $academicYears = AcademicYear::active()->orderBy('order')->get();
        $academicSubjects = AcademicSubject::active()->orderBy('name')->get();
        $parentCategories = QuestionCategory::active()->main()
                                          ->where('id', '!=', $questionCategory->id)
                                          ->orderBy('name')->get();

        return view('admin.question-categories.edit', compact(
            'questionCategory', 'academicYears', 'academicSubjects', 'parentCategories'
        ));
    }

    /**
     * تحديث التصنيف
     */
    public function update(Request $request, QuestionCategory $questionCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_subject_id' => 'required|exists:academic_subjects,id',
            'parent_id' => 'nullable|exists:question_categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $questionCategory->update($data);

        return redirect()->route('admin.question-categories.show', $questionCategory)
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    /**
     * حذف التصنيف
     */
    public function destroy(QuestionCategory $questionCategory)
    {
        // التحقق من عدم وجود أسئلة أو تصنيفات فرعية
        if ($questionCategory->questions()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف التصنيف لأنه يحتوي على أسئلة');
        }

        if ($questionCategory->children()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف التصنيف لأنه يحتوي على تصنيفات فرعية');
        }

        $questionCategory->delete();

        return redirect()->route('admin.question-categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }

    /**
     * إعادة ترتيب التصنيفات
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:question_categories,id',
            'categories.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->categories as $categoryData) {
            QuestionCategory::where('id', $categoryData['id'])
                          ->update(['order' => $categoryData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة ترتيب التصنيفات بنجاح'
        ]);
    }

    /**
     * الحصول على المواد حسب السنة الدراسية
     */
    public function getSubjectsByYear($year)
    {
        $subjects = AcademicSubject::where('academic_year_id', $year)
                                 ->active()
                                 ->orderBy('name')
                                 ->get(['id', 'name']);

        return response()->json($subjects);
    }
}

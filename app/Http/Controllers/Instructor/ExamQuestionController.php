<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedExam;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamQuestionController extends Controller
{
    /**
     * عرض صفحة إدارة أسئلة الاختبار
     */
    public function manage(AdvancedExam $exam)
    {
        $instructor = Auth::user();
        
        // التحقق من أن الاختبار يخص هذا المدرب (أونلاين أو أوفلاين)
        $owns = ($exam->advancedCourse && $exam->advancedCourse->instructor_id === $instructor->id)
            || ($exam->offlineCourse && $exam->offlineCourse->instructor_id === $instructor->id);
        if (!$owns) {
            abort(403, 'غير مسموح لك بالوصول لهذا الاختبار');
        }
        
        $exam->load('questions');
        
        // جلب بنوك الأسئلة الخاصة بالمدرب
        $questionBanks = QuestionBank::where('instructor_id', $instructor->id)
            ->orWhere('created_by', $instructor->id)
            ->where('is_active', true)
            ->withCount('questions')
            ->get();
        
        // جلب التصنيفات
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // جلب الأسئلة المتاحة من بنوك الأسئلة الخاصة بالمدرب
        $availableQuestions = Question::whereHas('questionBank', function($q) use ($instructor) {
                $q->where(function($query) use ($instructor) {
                    $query->where('instructor_id', $instructor->id)
                          ->orWhere('created_by', $instructor->id);
                });
            })
            ->where('is_active', true)
            ->with(['questionBank', 'category'])
            ->orderBy('question')
            ->get();
        
        return view('instructor.exams.manage-questions', compact('exam', 'questionBanks', 'categories', 'availableQuestions'));
    }
    
    /**
     * إضافة سؤال من بنك الأسئلة
     */
    public function addFromBank(Request $request, AdvancedExam $exam)
    {
        $instructor = Auth::user();
        
        // التحقق من أن الاختبار يخص هذا المدرب (أونلاين أو أوفلاين)
        $owns = ($exam->advancedCourse && $exam->advancedCourse->instructor_id === $instructor->id)
            || ($exam->offlineCourse && $exam->offlineCourse->instructor_id === $instructor->id);
        if (!$owns) {
            abort(403, 'غير مسموح لك بالوصول لهذا الاختبار');
        }
        
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'marks' => 'required|numeric|min:0.5',
            'order' => 'nullable|integer|min:1',
        ]);
        
        // التحقق من أن السؤال يخص المدرب
        $question = Question::whereHas('questionBank', function($q) use ($instructor) {
                $q->where(function($query) use ($instructor) {
                    $query->where('instructor_id', $instructor->id)
                          ->orWhere('created_by', $instructor->id);
                });
            })
            ->findOrFail($validated['question_id']);
        
        // التحقق من عدم وجود السؤال في الاختبار
        $exists = \App\Models\ExamQuestion::where('exam_id', $exam->id)
            ->where('question_id', $question->id)
            ->exists();
        
        if ($exists) {
            return back()->with('error', 'هذا السؤال موجود بالفعل في الاختبار');
        }
        
        // تحديد الترتيب
        $order = $validated['order'] ?? \App\Models\ExamQuestion::where('exam_id', $exam->id)->max('order') + 1;
        
        \App\Models\ExamQuestion::create([
            'exam_id' => $exam->id,
            'question_id' => $question->id,
            'order' => $order,
            'marks' => $validated['marks'],
        ]);
        
        return back()->with('success', 'تم إضافة السؤال بنجاح');
    }
    
    /**
     * إنشاء سؤال جديد
     */
    public function createNew(Request $request, AdvancedExam $exam)
    {
        $instructor = Auth::user();
        
        // التحقق من أن الاختبار يخص هذا المدرب (أونلاين أو أوفلاين)
        $owns = ($exam->advancedCourse && $exam->advancedCourse->instructor_id === $instructor->id)
            || ($exam->offlineCourse && $exam->offlineCourse->instructor_id === $instructor->id);
        if (!$owns) {
            abort(403, 'غير مسموح لك بالوصول لهذا الاختبار');
        }
        
        $validated = $request->validate([
            'question_bank_id' => 'required|exists:question_banks,id',
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,fill_blank,short_answer,essay',
            'options_text' => 'nullable|string',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|numeric|min:0.5',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'category_id' => 'nullable|exists:question_categories,id',
            'marks' => 'required|numeric|min:0.5',
            'order' => 'nullable|integer|min:1',
        ]);
        
        // معالجة الخيارات إذا كان نوع السؤال اختيار متعدد
        $options = null;
        if ($validated['type'] === 'multiple_choice' && $request->filled('options_text')) {
            $options = array_filter(array_map('trim', explode("\n", $request->options_text)));
            $options = array_values($options); // إعادة ترقيم المصفوفة
        }
        
        // التحقق من أن بنك الأسئلة يخص المدرب
        $questionBank = QuestionBank::where(function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id)
                  ->orWhere('created_by', $instructor->id);
            })
            ->findOrFail($validated['question_bank_id']);
        
        // إنشاء السؤال
        $question = Question::create([
            'question_bank_id' => $questionBank->id,
            'category_id' => $validated['category_id'] ?? null,
            'question' => $validated['question'],
            'type' => $validated['type'],
            'options' => $options,
            'correct_answer' => $validated['correct_answer'],
            'explanation' => $validated['explanation'] ?? null,
            'points' => $validated['points'],
            'difficulty_level' => $validated['difficulty_level'],
            'is_active' => true,
        ]);
        
        // إضافة السؤال للاختبار
        $order = $validated['order'] ?? \App\Models\ExamQuestion::where('exam_id', $exam->id)->max('order') + 1;
        
        \App\Models\ExamQuestion::create([
            'exam_id' => $exam->id,
            'question_id' => $question->id,
            'order' => $order,
            'marks' => $validated['marks'],
        ]);
        
        return back()->with('success', 'تم إنشاء السؤال وإضافته للاختبار بنجاح');
    }
    
    /**
     * حذف سؤال من الاختبار
     */
    public function remove(Request $request, AdvancedExam $exam, $questionId)
    {
        $instructor = Auth::user();
        
        // التحقق من أن الاختبار يخص هذا المدرب (أونلاين أو أوفلاين)
        $owns = ($exam->advancedCourse && $exam->advancedCourse->instructor_id === $instructor->id)
            || ($exam->offlineCourse && $exam->offlineCourse->instructor_id === $instructor->id);
        if (!$owns) {
            abort(403, 'غير مسموح لك بالوصول لهذا الاختبار');
        }
        
        \App\Models\ExamQuestion::where('exam_id', $exam->id)
            ->where('question_id', $questionId)
            ->delete();
        
        return back()->with('success', 'تم حذف السؤال من الاختبار بنجاح');
    }
    
    /**
     * إعادة ترتيب الأسئلة
     */
    public function reorder(Request $request, AdvancedExam $exam)
    {
        $instructor = Auth::user();
        
        // التحقق من أن الاختبار يخص هذا المدرب (أونلاين أو أوفلاين)
        $owns = ($exam->advancedCourse && $exam->advancedCourse->instructor_id === $instructor->id)
            || ($exam->offlineCourse && $exam->offlineCourse->instructor_id === $instructor->id);
        if (!$owns) {
            abort(403, 'غير مسموح لك بالوصول لهذا الاختبار');
        }
        
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:exam_questions,id',
            'questions.*.order' => 'required|integer|min:1',
        ]);
        
        foreach ($validated['questions'] as $item) {
            \App\Models\ExamQuestion::where('id', $item['id'])
                ->where('exam_id', $exam->id)
                ->update(['order' => $item['order']]);
        }
        
        return response()->json(['success' => true, 'message' => 'تم إعادة ترتيب الأسئلة بنجاح']);
    }
}

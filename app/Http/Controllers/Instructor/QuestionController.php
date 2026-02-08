<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instructor = Auth::user();
        
        $questions = Question::whereHas('questionBank', function($q) use ($instructor) {
                $q->where(function($query) use ($instructor) {
                    $query->where('instructor_id', $instructor->id)
                          ->orWhere('created_by', $instructor->id);
                });
            })
            ->with(['questionBank', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('instructor.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(QuestionBank $questionBank)
    {
        $instructor = Auth::user();
        
        // التحقق من أن بنك الأسئلة يخص هذا المدرب
        if ($questionBank->instructor_id !== $instructor->id && $questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا بنك الأسئلة');
        }
        
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('instructor.questions.create', compact('questionBank', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, QuestionBank $questionBank)
    {
        $instructor = Auth::user();
        
        // التحقق من أن بنك الأسئلة يخص هذا المدرب
        if ($questionBank->instructor_id !== $instructor->id && $questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا بنك الأسئلة');
        }
        
        $validated = $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,fill_blank,short_answer,essay',
            'options_text' => 'nullable|string',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|numeric|min:0.5',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'category_id' => 'nullable|exists:question_categories,id',
            'is_active' => 'boolean',
        ]);
        
        // معالجة الخيارات إذا كان نوع السؤال اختيار متعدد
        $options = null;
        if ($validated['type'] === 'multiple_choice' && $request->filled('options_text')) {
            $options = array_filter(array_map('trim', explode("\n", $request->options_text)));
            $options = array_values($options);
        }
        
        // معالجة الإجابة الصحيحة حسب نوع السؤال
        $correctAnswer = $validated['correct_answer'];
        if ($validated['type'] === 'fill_blank') {
            // لاملأ الفراغ، يمكن أن تكون عدة إجابات صحيحة
            $correctAnswer = array_filter(array_map('trim', explode("\n", $correctAnswer)));
            $correctAnswer = array_values($correctAnswer);
        } elseif ($validated['type'] === 'essay' || $validated['type'] === 'short_answer') {
            // للإجابة القصيرة والمقالية، نحفظ كسلسلة نصية
            $correctAnswer = trim($correctAnswer);
        } else {
            // للاختيار المتعدد وصح/خطأ، نحفظ كسلسلة نصية
            $correctAnswer = trim($correctAnswer);
        }
        
        $question = Question::create([
            'question_bank_id' => $questionBank->id,
            'category_id' => $validated['category_id'] ?? null,
            'question' => $validated['question'],
            'type' => $validated['type'],
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'explanation' => $validated['explanation'] ?? null,
            'points' => $validated['points'],
            'difficulty_level' => $validated['difficulty_level'],
            'is_active' => $request->has('is_active', true),
        ]);
        
        return redirect()->route('instructor.question-banks.show', $questionBank)
            ->with('success', 'تم إنشاء السؤال بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        $instructor = Auth::user();
        
        // التحقق من أن السؤال يخص هذا المدرب
        if ($question->questionBank->instructor_id !== $instructor->id && $question->questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا السؤال');
        }
        
        $question->load(['questionBank', 'category']);
        
        return view('instructor.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $instructor = Auth::user();
        
        // التحقق من أن السؤال يخص هذا المدرب
        if ($question->questionBank->instructor_id !== $instructor->id && $question->questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذا السؤال');
        }
        
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('instructor.questions.edit', compact('question', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $instructor = Auth::user();
        
        // التحقق من أن السؤال يخص هذا المدرب
        if ($question->questionBank->instructor_id !== $instructor->id && $question->questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذا السؤال');
        }
        
        $validated = $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,fill_blank,short_answer,essay',
            'options_text' => 'nullable|string',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|numeric|min:0.5',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'category_id' => 'nullable|exists:question_categories,id',
            'is_active' => 'boolean',
        ]);
        
        // معالجة الخيارات إذا كان نوع السؤال اختيار متعدد
        $options = null;
        if ($validated['type'] === 'multiple_choice' && $request->filled('options_text')) {
            $options = array_filter(array_map('trim', explode("\n", $request->options_text)));
            $options = array_values($options);
        } elseif ($validated['type'] !== 'multiple_choice') {
            $options = null;
        } else {
            $options = $question->options; // الاحتفاظ بالخيارات الحالية
        }
        
        // معالجة الإجابة الصحيحة حسب نوع السؤال
        $correctAnswer = $validated['correct_answer'];
        if ($validated['type'] === 'fill_blank') {
            // لاملأ الفراغ، يمكن أن تكون عدة إجابات صحيحة
            $correctAnswer = array_filter(array_map('trim', explode("\n", $correctAnswer)));
            $correctAnswer = array_values($correctAnswer);
        } elseif ($validated['type'] === 'essay' || $validated['type'] === 'short_answer') {
            // للإجابة القصيرة والمقالية، نحفظ كسلسلة نصية
            $correctAnswer = trim($correctAnswer);
        } else {
            // للاختيار المتعدد وصح/خطأ، نحفظ كسلسلة نصية
            $correctAnswer = trim($correctAnswer);
        }
        
        $question->update([
            'category_id' => $validated['category_id'] ?? null,
            'question' => $validated['question'],
            'type' => $validated['type'],
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'explanation' => $validated['explanation'] ?? null,
            'points' => $validated['points'],
            'difficulty_level' => $validated['difficulty_level'],
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('instructor.question-banks.show', $question->questionBank)
            ->with('success', 'تم تحديث السؤال بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $instructor = Auth::user();
        
        // التحقق من أن السؤال يخص هذا المدرب
        if ($question->questionBank->instructor_id !== $instructor->id && $question->questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بحذف هذا السؤال');
        }
        
        $questionBank = $question->questionBank;
        $question->delete();
        
        return redirect()->route('instructor.question-banks.show', $questionBank)
            ->with('success', 'تم حذف السؤال بنجاح');
    }
}

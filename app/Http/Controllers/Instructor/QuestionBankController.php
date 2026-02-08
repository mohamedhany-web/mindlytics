<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $instructor = Auth::user();
        
        $questionBanks = QuestionBank::where(function($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id)
                  ->orWhere('created_by', $instructor->id);
            })
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('instructor.question-banks.index', compact('questionBanks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $instructor = Auth::user();
        
        // جلب التصنيفات المتاحة
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('instructor.question-banks.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $instructor = Auth::user();
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'is_active' => 'boolean',
        ]);
        
        $validated['instructor_id'] = $instructor->id;
        $validated['created_by'] = $instructor->id;
        $validated['is_active'] = $request->has('is_active');
        
        // إذا لم يتم تحديد difficulty، ضع null
        if (empty($validated['difficulty'])) {
            $validated['difficulty'] = null;
        }
        
        $questionBank = QuestionBank::create($validated);
        
        return redirect()->route('instructor.question-banks.show', $questionBank)
            ->with('success', 'تم إنشاء بنك الأسئلة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionBank $questionBank)
    {
        $instructor = Auth::user();
        
        // التحقق من أن بنك الأسئلة يخص هذا المدرب
        if ($questionBank->instructor_id !== $instructor->id && $questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا بنك الأسئلة');
        }
        
        $questionBank->load(['questions.category']);
        
        // جلب التصنيفات
        $categories = QuestionCategory::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('instructor.question-banks.show', compact('questionBank', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuestionBank $questionBank)
    {
        $instructor = Auth::user();
        
        // التحقق من أن بنك الأسئلة يخص هذا المدرب
        if ($questionBank->instructor_id !== $instructor->id && $questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذا بنك الأسئلة');
        }
        
        return view('instructor.question-banks.edit', compact('questionBank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuestionBank $questionBank)
    {
        $instructor = Auth::user();
        
        // التحقق من أن بنك الأسئلة يخص هذا المدرب
        if ($questionBank->instructor_id !== $instructor->id && $questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بتعديل هذا بنك الأسئلة');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        // إذا لم يتم تحديد difficulty، ضع null
        if (empty($validated['difficulty'])) {
            $validated['difficulty'] = null;
        }
        
        $questionBank->update($validated);
        
        return redirect()->route('instructor.question-banks.show', $questionBank)
            ->with('success', 'تم تحديث بنك الأسئلة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuestionBank $questionBank)
    {
        $instructor = Auth::user();
        
        // التحقق من أن بنك الأسئلة يخص هذا المدرب
        if ($questionBank->instructor_id !== $instructor->id && $questionBank->created_by !== $instructor->id) {
            abort(403, 'غير مسموح لك بحذف هذا بنك الأسئلة');
        }
        
        $questionBank->delete();
        
        return redirect()->route('instructor.question-banks.index')
            ->with('success', 'تم حذف بنك الأسئلة بنجاح');
    }
}

<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\LearningPattern;
use App\Models\LearningPatternAttempt;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\QuestionBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LearningPatternController extends Controller
{
    /**
     * عرض قائمة الأنماط للكورس
     */
    public function index(AdvancedCourse $course)
    {
        $instructor = Auth::user();
        
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا الكورس');
        }
        
        $patterns = $course->learningPatterns()
            ->with('instructor')
            ->orderBy('order')
            ->get();
        
        return view('instructor.learning-patterns.index', compact('course', 'patterns'));
    }

    /**
     * عرض نموذج إنشاء نمط جديد
     */
    public function create(AdvancedCourse $course)
    {
        $instructor = Auth::user();
        
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا الكورس');
        }
        
        $sections = $course->sections()->active()->ordered()->get();
        $types = LearningPattern::getAvailableTypes();
        $selectedSectionId = request('section_id');
        
        $questionBanks = QuestionBank::where(function ($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id)->orWhere('created_by', $instructor->id);
        })
            ->where('is_active', true)
            ->with(['questions' => function ($q) {
                $q->where('is_active', true)->whereIn('type', ['multiple_choice', 'true_false']);
            }])
            ->orderBy('title')
            ->get()
            ->map(function ($bank) {
                $bank->setRelation('questions', $bank->questions->map(function ($q) {
                    $options = is_array($q->options) ? $q->options : [];
                    $correct = $q->correct_answer;
                    if (is_array($correct)) {
                        $correct = $correct[0] ?? null;
                    }
                    if ($q->type === 'multiple_choice' && $options) {
                        if (!is_numeric($correct)) {
                            $idx = array_search($correct, $options);
                            $correct = $idx !== false ? (string) $idx : '0';
                        } else {
                            $correct = (string) (int) $correct;
                        }
                    }
                    return [
                        'id' => $q->id,
                        'question' => $q->question,
                        'type' => $q->type,
                        'options' => $options,
                        'correct_answer' => $correct,
                    ];
                }));
                return $bank;
            });
        
        return view('instructor.learning-patterns.create', compact('course', 'sections', 'types', 'selectedSectionId', 'questionBanks'));
    }

    /**
     * حفظ نمط جديد
     */
    public function store(Request $request, AdvancedCourse $course)
    {
        $instructor = Auth::user();
        
        if ($course->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا الكورس');
        }
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:' . implode(',', array_keys(LearningPattern::getAvailableTypes())),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'points' => 'nullable|integer|min:0',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'difficulty_level' => 'required|integer|min:1|max:5',
            'is_required' => 'boolean',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'pattern_data' => 'nullable|array',
            'course_section_id' => 'nullable|exists:course_sections,id',
            'order' => 'nullable|integer',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $pattern = LearningPattern::create([
            'advanced_course_id' => $course->id,
            'instructor_id' => $instructor->id,
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'pattern_data' => $request->pattern_data ?? [],
            'points' => $request->points ?? 0,
            'time_limit_minutes' => $request->time_limit_minutes,
            'difficulty_level' => $request->difficulty_level,
            'is_required' => $request->boolean('is_required'),
            'allow_multiple_attempts' => $request->boolean('allow_multiple_attempts', true),
            'max_attempts' => $request->max_attempts,
            'order' => $request->order ?? 0,
        ]);
        
        // إضافة النمط للمنهج إذا تم تحديد قسم
        if ($request->course_section_id) {
            $section = CourseSection::findOrFail($request->course_section_id);
            
            // الحصول على آخر ترتيب في القسم
            $lastOrder = $section->items()->max('order') ?? 0;
            
            CurriculumItem::create([
                'course_section_id' => $section->id,
                'item_type' => LearningPattern::class,
                'item_id' => $pattern->id,
                'order' => $lastOrder + 1,
                'is_active' => true,
            ]);
        }
        
        return redirect()->route('instructor.learning-patterns.index', $course)
            ->with('success', 'تم إنشاء النمط التعليمي بنجاح');
    }

    /**
     * عرض تفاصيل النمط
     */
    public function show(AdvancedCourse $course, LearningPattern $pattern)
    {
        $instructor = Auth::user();
        
        if ($course->instructor_id !== $instructor->id || $pattern->advanced_course_id !== $course->id) {
            abort(403);
        }
        
        $pattern->load(['attempts' => function($query) {
            $query->latest()->limit(10);
        }, 'attempts.user']);
        
        return view('instructor.learning-patterns.show', compact('course', 'pattern'));
    }

    /**
     * عرض نموذج تعديل النمط
     */
    public function edit(AdvancedCourse $course, LearningPattern $pattern)
    {
        $instructor = Auth::user();
        
        if ($course->instructor_id !== $instructor->id || $pattern->advanced_course_id !== $course->id) {
            abort(403);
        }
        
        $sections = $course->sections()->active()->ordered()->get();
        $types = LearningPattern::getAvailableTypes();
        $currentSection = $pattern->curriculumItems()->first()?->section;
        
        $questionBanks = QuestionBank::where(function ($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id)->orWhere('created_by', $instructor->id);
        })
            ->where('is_active', true)
            ->with(['questions' => function ($q) {
                $q->where('is_active', true)->whereIn('type', ['multiple_choice', 'true_false']);
            }])
            ->orderBy('title')
            ->get()
            ->map(function ($bank) {
                $bank->setRelation('questions', $bank->questions->map(function ($q) {
                    $options = is_array($q->options) ? $q->options : [];
                    $correct = $q->correct_answer;
                    if (is_array($correct)) {
                        $correct = $correct[0] ?? null;
                    }
                    if ($q->type === 'multiple_choice' && $options) {
                        if (!is_numeric($correct)) {
                            $idx = array_search($correct, $options);
                            $correct = $idx !== false ? (string) $idx : '0';
                        } else {
                            $correct = (string) (int) $correct;
                        }
                    }
                    return [
                        'id' => $q->id,
                        'question' => $q->question,
                        'type' => $q->type,
                        'options' => $options,
                        'correct_answer' => $correct,
                    ];
                }));
                return $bank;
            });
        
        return view('instructor.learning-patterns.edit', compact('course', 'pattern', 'sections', 'types', 'currentSection', 'questionBanks'));
    }

    /**
     * تحديث النمط
     */
    public function update(Request $request, AdvancedCourse $course, LearningPattern $pattern)
    {
        $instructor = Auth::user();
        
        if ($course->instructor_id !== $instructor->id || $pattern->advanced_course_id !== $course->id) {
            abort(403);
        }
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:' . implode(',', array_keys(LearningPattern::getAvailableTypes())),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'points' => 'nullable|integer|min:0',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'difficulty_level' => 'required|integer|min:1|max:5',
            'is_required' => 'boolean',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'pattern_data' => 'nullable|array',
            'course_section_id' => 'nullable|exists:course_sections,id',
            'order' => 'nullable|integer',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $pattern->update([
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'pattern_data' => $request->pattern_data ?? $pattern->pattern_data,
            'points' => $request->points ?? 0,
            'time_limit_minutes' => $request->time_limit_minutes,
            'difficulty_level' => $request->difficulty_level,
            'is_required' => $request->boolean('is_required'),
            'allow_multiple_attempts' => $request->boolean('allow_multiple_attempts', true),
            'max_attempts' => $request->max_attempts,
            'order' => $request->order ?? $pattern->order,
        ]);
        
        // تحديث القسم في المنهج
        $currentItem = $pattern->curriculumItems()->first();
        
        if ($request->course_section_id) {
            if ($currentItem && $currentItem->course_section_id != $request->course_section_id) {
                // نقل إلى قسم جديد
                $section = CourseSection::findOrFail($request->course_section_id);
                $lastOrder = $section->items()->max('order') ?? 0;
                
                $currentItem->update([
                    'course_section_id' => $section->id,
                    'order' => $lastOrder + 1,
                ]);
            } elseif (!$currentItem) {
                // إضافة للمنهج
                $section = CourseSection::findOrFail($request->course_section_id);
                $lastOrder = $section->items()->max('order') ?? 0;
                
                CurriculumItem::create([
                    'course_section_id' => $section->id,
                    'item_type' => LearningPattern::class,
                    'item_id' => $pattern->id,
                    'order' => $lastOrder + 1,
                    'is_active' => true,
                ]);
            }
        } elseif ($currentItem) {
            // إزالة من المنهج
            $currentItem->delete();
        }
        
        return redirect()->route('instructor.learning-patterns.index', $course)
            ->with('success', 'تم تحديث النمط التعليمي بنجاح');
    }

    /**
     * حذف النمط
     */
    public function destroy(AdvancedCourse $course, LearningPattern $pattern)
    {
        $instructor = Auth::user();
        
        if ($course->instructor_id !== $instructor->id || $pattern->advanced_course_id !== $course->id) {
            abort(403);
        }
        
        $pattern->delete();
        
        return redirect()->route('instructor.learning-patterns.index', $course)
            ->with('success', 'تم حذف النمط التعليمي بنجاح');
    }

    /**
     * حذف محاولة طالب (يسمح للطالب بإعادة المحاولة عند الرغبة)
     */
    public function destroyAttempt(AdvancedCourse $course, LearningPattern $pattern, LearningPatternAttempt $attempt)
    {
        $instructor = Auth::user();
        
        if ($course->instructor_id !== $instructor->id || $pattern->advanced_course_id !== $course->id) {
            abort(403);
        }
        
        if ($attempt->learning_pattern_id != $pattern->id) {
            abort(404);
        }
        
        $wasCompleted = $attempt->status === 'completed';
        $attempt->delete();
        
        if ($wasCompleted && $pattern->total_completions > 0) {
            $pattern->decrement('total_completions');
        }
        if ($pattern->total_attempts > 0) {
            $pattern->decrement('total_attempts');
        }
        
        return redirect()->route('instructor.learning-patterns.show', [$course, $pattern])
            ->with('success', 'تم حذف المحاولة. يمكن للطالب إعادة المحاولة إن رغب.');
    }
}

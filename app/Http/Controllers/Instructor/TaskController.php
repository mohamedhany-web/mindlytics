<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $instructor = Auth::user();
        
        // جلب المهام الخاصة بالمدرب
        $query = Task::where('user_id', $instructor->id)
            ->with(['relatedCourse', 'relatedLecture']);

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(20);

        // إحصائيات
        $stats = [
            'total' => Task::where('user_id', $instructor->id)->count(),
            'pending' => Task::where('user_id', $instructor->id)->where('status', 'pending')->count(),
            'in_progress' => Task::where('user_id', $instructor->id)->where('status', 'in_progress')->count(),
            'completed' => Task::where('user_id', $instructor->id)->where('status', 'completed')->count(),
        ];

        return view('instructor.tasks.index', compact('tasks', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $instructor = Auth::user();
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();
        
        // جلب المحاضرات إذا تم اختيار كورس
        $lectures = collect();
        if (request()->filled('course_id')) {
            $lectures = \App\Models\Lecture::where('course_id', request('course_id'))
                ->where('instructor_id', $instructor->id)
                ->orderBy('scheduled_at', 'desc')
                ->get();
        }
        
        return view('instructor.tasks.create', compact('courses', 'lectures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'related_course_id' => 'nullable|exists:advanced_courses,id',
            'related_lecture_id' => 'nullable|exists:lectures,id',
            'tags' => 'nullable|array',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';
        $task = Task::create($validated);

        return redirect()->route('instructor.tasks.show', $task)
            ->with('success', 'تم إنشاء المهمة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // التحقق من أن المهمة تخص المدرب
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->load(['relatedCourse', 'relatedLecture']);

        return view('instructor.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // التحقق من أن المهمة تخص المدرب
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $instructor = Auth::user();
        $courses = AdvancedCourse::where('instructor_id', $instructor->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        // جلب المحاضرات للكورس المحدد
        $lectures = collect();
        if ($task->related_course_id) {
            $lectures = \App\Models\Lecture::where('course_id', $task->related_course_id)
                ->where('instructor_id', $instructor->id)
                ->orderBy('scheduled_at', 'desc')
                ->get();
        }

        return view('instructor.tasks.edit', compact('task', 'courses', 'lectures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        // التحقق من أن المهمة تخص المدرب
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'related_course_id' => 'nullable|exists:advanced_courses,id',
            'related_lecture_id' => 'nullable|exists:lectures,id',
            'tags' => 'nullable|array',
        ]);

        if ($request->status === 'completed' && !$task->completed_at) {
            $validated['completed_at'] = now();
        }

        $task->update($validated);

        return redirect()->route('instructor.tasks.show', $task)
            ->with('success', 'تم تحديث المهمة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // التحقق من أن المهمة تخص المدرب
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('instructor.tasks.index')
            ->with('success', 'تم حذف المهمة بنجاح');
    }

    /**
     * Get lectures for a course (AJAX)
     */
    public function getLectures(Request $request)
    {
        $instructor = Auth::user();
        
        $lectures = \App\Models\Lecture::where('course_id', $request->course_id)
            ->where('instructor_id', $instructor->id)
            ->orderBy('scheduled_at', 'desc')
            ->get(['id', 'title', 'scheduled_at']);
        
        return response()->json($lectures);
    }
}

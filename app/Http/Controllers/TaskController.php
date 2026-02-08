<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Task::where('user_id', Auth::id())
            ->with(['relatedCourse', 'relatedLecture']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(20);

        $stats = [
            'total' => Task::where('user_id', Auth::id())->count(),
            'pending' => Task::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'in_progress' => Task::where('user_id', Auth::id())->where('status', 'in_progress')->count(),
            'completed' => Task::where('user_id', Auth::id())->where('status', 'completed')->count(),
        ];

        return view('tasks.index', compact('tasks', 'stats'));
    }

    public function create()
    {
        return view('tasks.create');
    }

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
        $task = Task::create($validated);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'تم إنشاء المهمة بنجاح');
    }

    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->load(['relatedCourse', 'relatedLecture', 'comments.user']);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'tags' => 'nullable|array',
        ]);

        if ($request->status === 'completed' && !$task->completed_at) {
            $validated['completed_at'] = now();
        }

        $task->update($validated);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'تم تحديث المهمة بنجاح');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'تم حذف المهمة بنجاح');
    }
}

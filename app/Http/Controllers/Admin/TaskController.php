<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskDeliverable;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * قائمة مهام المدربين (المسندة من الإدارة)
     */
    public function index(Request $request)
    {
        $instructorIds = User::whereIn('role', ['instructor', 'teacher'])->pluck('id');

        $query = Task::with(['user', 'relatedCourse', 'relatedLecture'])
            ->whereIn('user_id', $instructorIds);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(20);

        $stats = [
            'total' => Task::whereIn('user_id', $instructorIds)->count(),
            'pending' => Task::whereIn('user_id', $instructorIds)->where('status', 'pending')->count(),
            'in_progress' => Task::whereIn('user_id', $instructorIds)->where('status', 'in_progress')->count(),
            'completed' => Task::whereIn('user_id', $instructorIds)->where('status', 'completed')->count(),
        ];

        $instructors = User::whereIn('role', ['instructor', 'teacher'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.tasks.index', compact('tasks', 'stats', 'instructors'));
    }

    /**
     * نموذج إضافة مهمة لمدرب
     */
    public function create()
    {
        $users = User::whereIn('role', ['instructor', 'teacher'])
            ->orderBy('name')
            ->get(['id', 'name']);
        return view('admin.tasks.create', compact('users'));
    }

    /**
     * حفظ مهمة جديدة لمدرب
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'related_course_id' => 'nullable|exists:advanced_courses,id',
            'related_lecture_id' => 'nullable|exists:lectures,id',
        ]);

        $validated['status'] = 'pending';
        $validated['assigned_by'] = auth()->id();
        Task::create($validated);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'تم إضافة المهمة للمدرب بنجاح');
    }

    /**
     * عرض تفاصيل المهمة
     */
    public function show(Task $task)
    {
        $task->load(['user', 'relatedCourse', 'relatedLecture', 'comments.user', 'deliverables.reviewer']);
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * نموذج تعديل المهمة
     */
    public function edit(Task $task)
    {
        $users = User::whereIn('role', ['instructor', 'teacher'])->orderBy('name')->get(['id', 'name']);
        $task->load(['relatedCourse', 'relatedLecture']);
        return view('admin.tasks.edit', compact('task', 'users'));
    }

    /**
     * تحديث المهمة
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'related_course_id' => 'nullable|exists:advanced_courses,id',
            'related_lecture_id' => 'nullable|exists:lectures,id',
        ]);

        if ($request->status === 'completed' && !$task->completed_at) {
            $validated['completed_at'] = now();
        }

        $task->update($validated);

        return redirect()->route('admin.tasks.show', $task)
            ->with('success', 'تم تحديث المهمة بنجاح');
    }

    /**
     * حذف المهمة
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.index')
            ->with('success', 'تم حذف المهمة');
    }

    /**
     * تعليم المهمة كمكتملة
     */
    public function complete(Task $task)
    {
        $task->update(['status' => 'completed', 'completed_at' => now()]);
        return redirect()->back()->with('success', 'تم تعليم المهمة كمكتملة');
    }

    /**
     * إضافة تعليق على المهمة
     */
    public function addComment(Request $request, Task $task)
    {
        $request->validate(['comment' => 'required|string|max:2000']);

        \App\Models\TaskComment::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'تم إضافة التعليق');
    }

    /**
     * مراجعة تسليم مدرب (اعتماد / رفض / يحتاج مراجعة)
     */
    public function reviewDeliverable(Request $request, Task $task, TaskDeliverable $deliverable)
    {
        if ($deliverable->task_id !== $task->id) {
            abort(404);
        }
        $request->validate([
            'status' => 'required|in:approved,rejected,needs_revision',
            'feedback' => 'nullable|string|max:2000',
        ]);
        $deliverable->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        if ($request->status === 'approved' && $task->status !== 'completed') {
            $task->update(['progress' => min(100, (int) $task->progress + 25)]);
        }
        return redirect()->back()->with('success', 'تم حفظ المراجعة');
    }
}

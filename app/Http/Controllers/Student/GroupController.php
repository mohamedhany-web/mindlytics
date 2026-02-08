<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Group;
use App\Models\GroupMessage;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * قائمة مجموعات الطالب
     */
    public function index()
    {
        $groups = auth()->user()
            ->groups()
            ->with(['course', 'leader', 'members'])
            ->where('groups.status', 'active')
            ->orderBy('groups.name')
            ->get();

        return view('student.groups.index', compact('groups'));
    }

    /**
     * عرض مجموعة واحدة: المحادثة والأعضاء
     */
    public function show(Group $group)
    {
        if (!auth()->user()->groups()->where('groups.id', $group->id)->exists()) {
            abort(403, 'غير مسموح لك بعرض هذه المجموعة');
        }
        if ($group->status !== 'active') {
            abort(404);
        }

        $group->load(['course', 'leader', 'members', 'messages.user']);
        return view('student.groups.show', compact('group'));
    }

    /**
     * صفحة واجبات المجموعة
     */
    public function assignments(Group $group)
    {
        if (!auth()->user()->groups()->where('groups.id', $group->id)->exists()) {
            abort(403, 'غير مسموح لك بعرض هذه المجموعة');
        }
        if ($group->status !== 'active') {
            abort(404);
        }

        $group->load(['course', 'leader', 'members']);
        $assignments = $group->assignments()
            ->where('status', 'published')
            ->orderBy('due_date')
            ->get();

        foreach ($assignments as $a) {
            $a->group_submission = AssignmentSubmission::where('assignment_id', $a->id)
                ->where('group_id', $group->id)
                ->first();
        }

        return view('student.groups.assignments', compact('group', 'assignments'));
    }

    /**
     * جلب الرسائل (للمحادثة الفورية - استطلاع)
     */
    public function getMessages(Request $request, Group $group)
    {
        if (!auth()->user()->groups()->where('groups.id', $group->id)->exists()) {
            abort(403);
        }

        $query = $group->messages()->with('user')->orderBy('id');
        if ($request->filled('after_id')) {
            $query->where('id', '>', (int) $request->after_id);
        }
        $messages = $query->get()->map(function ($msg) {
            return [
                'id' => $msg->id,
                'user_id' => $msg->user_id,
                'user_name' => $msg->user->name ?? 'غير معروف',
                'body' => $msg->body,
                'created_at' => $msg->created_at->toIso8601String(),
                'created_at_human' => $msg->created_at->diffForHumans(),
            ];
        });

        return response()->json(['messages' => $messages]);
    }

    /**
     * إرسال رسالة في محادثة المجموعة
     */
    public function storeMessage(Request $request, Group $group)
    {
        if (!auth()->user()->groups()->where('groups.id', $group->id)->exists()) {
            abort(403);
        }

        $request->validate(['body' => 'required|string|max:2000']);

        $msg = GroupMessage::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);
        $msg->load('user');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $msg->id,
                    'user_id' => $msg->user_id,
                    'user_name' => $msg->user->name ?? 'غير معروف',
                    'body' => $msg->body,
                    'created_at' => $msg->created_at->toIso8601String(),
                    'created_at_human' => $msg->created_at->diffForHumans(),
                ],
            ]);
        }

        return back()->with('success', 'تم إرسال الرسالة.');
    }

    /**
     * تسليم واجب جماعي نيابة عن المجموعة
     */
    public function submitAssignment(Request $request, Group $group, Assignment $assignment)
    {
        if (!auth()->user()->groups()->where('groups.id', $group->id)->exists()) {
            abort(403);
        }
        if ($assignment->group_id != $group->id) {
            abort(404);
        }

        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('group_id', $group->id)
            ->first();
        if ($existing) {
            return back()->with('error', 'تم تسليم هذا الواجب مسبقاً من المجموعة.');
        }

        $request->validate([
            'content' => 'nullable|string|max:10000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $file->store('group-submissions', 'public');
            }
        }

        AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'group_id' => $group->id,
            'student_id' => auth()->id(),
            'content' => $request->content,
            'attachments' => $attachments,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        return back()->with('success', 'تم تسليم الواجب بنجاح.');
    }
}

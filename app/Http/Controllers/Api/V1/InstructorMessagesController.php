<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\DirectMessage;
use App\Models\DirectMessageThread;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorMessagesController extends Controller
{
    private function teachingCourseIds(Request $request)
    {
        $user = $request->user();
        $directCourseIds = AdvancedCourse::where('instructor_id', $user->id)->pluck('id');
        $assignedFromPaths = $user->teachingLearningPaths()->get()->flatMap(function ($ay) {
            $ids = json_decode($ay->pivot->assigned_courses ?? '[]', true);
            return is_array($ids) ? $ids : [];
        });
        return $directCourseIds->merge($assignedFromPaths)->unique()->filter()->values();
    }

    public function threads(Request $request): JsonResponse
    {
        $user = $request->user();
        $q = DirectMessageThread::query()
            ->where('instructor_id', $user->id)
            ->with(['student:id,name,email,profile_image', 'course:id,title,title_en'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        $p = $q->paginate(min(max((int) $request->input('per_page', 25), 1), 50));

        $data = $p->getCollection()->map(function (DirectMessageThread $t) {
            return [
                'id' => $t->id,
                'course' => $t->course ? [
                    'id' => $t->course->id,
                    'title' => [
                        'ar' => $t->course->title,
                        'en' => $t->course->title_en ?: $t->course->title,
                    ],
                ] : null,
                'student' => [
                    'id' => $t->student?->id,
                    'name' => $t->student?->name,
                    'email' => $t->student?->email,
                    'profile_image_url' => $t->student?->profile_image_url,
                ],
                'last_message_at' => $t->last_message_at?->toIso8601String(),
            ];
        })->values();

        return response()->json([
            'threads' => $data,
            'pagination' => [
                'current_page' => $p->currentPage(),
                'last_page' => $p->lastPage(),
                'per_page' => $p->perPage(),
                'total' => $p->total(),
            ],
        ]);
    }

    public function startThread(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer'],
            'course_id' => ['nullable', 'integer'],
        ]);

        $instructor = $request->user();
        $student = User::find((int) $data['student_id']);
        if (! $student || ! $student->isStudent()) {
            return response()->json(['message' => 'طالب غير صالح'], 422);
        }

        $courseId = isset($data['course_id']) ? (int) $data['course_id'] : null;
        if ($courseId) {
            $ids = $this->teachingCourseIds($request);
            if (! $ids->contains($courseId)) {
                return response()->json(['message' => 'كورس غير مسموح'], 403);
            }
            if (! StudentCourseEnrollment::where('advanced_course_id', $courseId)->where('user_id', $student->id)->exists()) {
                return response()->json(['message' => 'الطالب غير مسجل في هذا الكورس'], 422);
            }
        }

        $thread = DirectMessageThread::firstOrCreate([
            'course_id' => $courseId,
            'instructor_id' => $instructor->id,
            'student_id' => $student->id,
        ]);

        return response()->json(['thread_id' => $thread->id], 201);
    }

    public function messages(Request $request, DirectMessageThread $thread): JsonResponse
    {
        if ($thread->instructor_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $perPage = min(max((int) $request->input('per_page', 50), 1), 100);
        $p = DirectMessage::query()
            ->where('thread_id', $thread->id)
            ->with(['sender:id,name,role,profile_image'])
            ->orderByDesc('id')
            ->paginate($perPage);

        $items = $p->getCollection()->map(function (DirectMessage $m) {
            return [
                'id' => $m->id,
                'body' => $m->body,
                'created_at' => $m->created_at?->toIso8601String(),
                'sender' => [
                    'id' => $m->sender?->id,
                    'name' => $m->sender?->name,
                    'role' => $m->sender?->role,
                    'profile_image_url' => $m->sender?->profile_image_url,
                ],
            ];
        })->values();

        return response()->json([
            'thread' => [
                'id' => $thread->id,
                'course_id' => $thread->course_id,
                'student_id' => $thread->student_id,
            ],
            'messages' => $items,
            'pagination' => [
                'current_page' => $p->currentPage(),
                'last_page' => $p->lastPage(),
                'per_page' => $p->perPage(),
                'total' => $p->total(),
            ],
        ]);
    }

    public function send(Request $request, DirectMessageThread $thread): JsonResponse
    {
        if ($thread->instructor_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:4000'],
        ]);

        $m = DirectMessage::create([
            'thread_id' => $thread->id,
            'sender_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);

        $thread->update(['last_message_at' => now()]);

        return response()->json(['message_id' => $m->id], 201);
    }
}


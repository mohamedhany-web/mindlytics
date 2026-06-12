<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * مجموعات الكورس للطالب (كما في /student/groups على الويب) — للتطبيق.
 */
class StudentGroupsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $groups = $user->groups()
            ->select('groups.*')
            ->with(['course:id,title,title_en', 'leader:id,name'])
            ->withCount('members')
            ->where('groups.status', 'active')
            ->orderBy('groups.name')
            ->get()
            ->map(function (Group $group) {
                $course = $group->course;

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'description' => $group->description,
                    'members_count' => (int) $group->members_count,
                    'leader_name' => $group->leader?->name,
                    'course' => $course ? [
                        'id' => $course->id,
                        'title' => [
                            'ar' => $course->title,
                            'en' => $course->title_en ?: $course->title,
                        ],
                    ] : null,
                    'web_path' => '/student/groups/'.$group->id,
                ];
            })
            ->values();

        return response()->json([
            'groups' => $groups,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\StudentCourseEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorCoursesController extends Controller
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

    public function index(Request $request): JsonResponse
    {
        $ids = $this->teachingCourseIds($request);

        $rows = $ids->isEmpty()
            ? collect()
            : AdvancedCourse::whereIn('id', $ids)
                ->with(['academicSubject', 'academicYear'])
                ->withCount(['enrollments as active_students_count' => function ($q) {
                    $q->where('status', 'active');
                }])
                ->orderByDesc('created_at')
                ->get();

        $payload = $rows->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => [
                    'ar' => $course->title,
                    'en' => $course->title_en ?: $course->title,
                ],
                'subject_name' => [
                    'ar' => $course->academicSubject?->name_ar ?? $course->academicSubject?->name ?? '',
                    'en' => $course->academicSubject?->name_en ?? $course->academicSubject?->name ?? '',
                ],
                'year_name' => [
                    'ar' => $course->academicYear?->name_ar ?? $course->academicYear?->name ?? '',
                    'en' => $course->academicYear?->name_en ?? $course->academicYear?->name ?? '',
                ],
                'active_students_count' => (int) ($course->active_students_count ?? 0),
                'is_active' => (bool) ($course->is_active ?? true),
            ];
        })->values();

        return response()->json(['courses' => $payload]);
    }

    public function students(Request $request, AdvancedCourse $course): JsonResponse
    {
        $ids = $this->teachingCourseIds($request);
        if (! $ids->contains($course->id)) {
            return response()->json([
                'message' => 'غير مسموح لك بالوصول لهذا الكورس.',
                'code' => 'course_not_owned',
            ], 403);
        }

        $q = StudentCourseEnrollment::where('advanced_course_id', $course->id)
            ->with('user:id,name,email,phone')
            ->where('status', 'active')
            ->latest('id');

        if ($search = trim((string) $request->query('q'))) {
            $q->whereHas('user', function ($u) use ($search) {
                $u->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        $rows = $q->paginate(25);

        $payload = $rows->getCollection()->map(function ($en) {
            return [
                'id' => $en->id,
                'student' => [
                    'id' => $en->user?->id,
                    'name' => $en->user?->name,
                    'email' => $en->user?->email,
                    'phone' => $en->user?->phone,
                ],
                'enrolled_at' => $en->created_at?->toISOString(),
            ];
        })->values();

        return response()->json([
            'students' => $payload,
            'pagination' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'per_page' => $rows->perPage(),
                'total' => $rows->total(),
            ],
        ]);
    }
}


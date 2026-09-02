<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\LearningPathEnrollment;
use App\Models\StudentCourseEnrollment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LearningPathController extends Controller
{
    /**
     * قائمة كل المسارات المشترك فيها الطالب
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->isStudent()) {
            abort(403, __('student.lp_forbidden'));
        }

        $enrollments = LearningPathEnrollment::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['learningPath' => function ($q) {
                $q->with(['linkedCourses' => function ($cq) {
                    $cq->where('is_active', true);
                }, 'academicSubjects']);
            }])
            ->orderByDesc('enrolled_at')
            ->get()
            ->filter(fn ($e) => $e->learningPath);

        $paths = $enrollments->map(function (LearningPathEnrollment $enrollment) use ($user) {
            $year = $enrollment->learningPath;
            $summary = $this->summarizePathCourses($year, $user->id);

            // Keep enrollment.progress in sync for sidebar/elsewhere
            if ((float) $enrollment->progress !== (float) $summary['progress']) {
                $enrollment->update(['progress' => $summary['progress']]);
            }

            return (object) [
                'id' => $year->id,
                'name' => $year->name,
                'description' => $year->description,
                'slug' => Str::slug($year->name),
                'code' => $year->code,
                'icon' => $year->icon,
                'progress' => $summary['progress'],
                'courses_count' => $summary['courses_count'],
                'enrolled_courses_count' => $summary['enrolled_courses_count'],
                'completed_courses_count' => $summary['completed_courses_count'],
                'remaining_courses_count' => $summary['remaining_courses_count'],
                'enrolled_at' => $enrollment->enrolled_at ?? $enrollment->created_at,
            ];
        })->values();

        return view('student.learning-path.index', compact('paths'));
    }

    /**
     * تفاصيل مسار واحد (الكورسات / الفيديو / التقدم)
     */
    public function show($slug)
    {
        $user = Auth::user();

        if (!$user->isStudent()) {
            abort(403, __('student.lp_forbidden'));
        }

        $academicYear = AcademicYear::active()
            ->with(['linkedCourses' => function ($query) {
                $query->where('is_active', true)
                    ->with(['academicSubject', 'academicYear', 'instructor'])
                    ->withCount('lessons');
            }, 'academicSubjects'])
            ->get()
            ->first(function ($year) use ($slug) {
                return Str::slug($year->name) === $slug;
            });

        if (!$academicYear) {
            abort(404, __('student.lp_not_found'));
        }

        $enrollment = LearningPathEnrollment::where('user_id', $user->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return redirect()->route('public.learning-path.show', ['slug' => $slug])
                ->with('error', __('student.lp_not_enrolled'));
        }

        $built = $this->buildPathDetail($academicYear, $user->id);
        $enrollment->update(['progress' => $built['progress']]);

        $learningPath = (object) array_merge([
            'id' => $academicYear->id,
            'name' => $academicYear->name,
            'description' => $academicYear->description,
            'video_url' => $academicYear->video_url,
            'slug' => Str::slug($academicYear->name),
            'icon' => $academicYear->icon,
            'color' => $academicYear->color,
            'code' => $academicYear->code,
        ], $built);

        return view('student.learning-path.show', compact('learningPath', 'enrollment'));
    }

    /**
     * @return array{courses: Collection, courses_count: int, enrolled_courses_count: int, completed_courses_count: int, remaining_courses_count: int, progress: float, continue_course: mixed}
     */
    private function buildPathDetail(AcademicYear $academicYear, int $userId): array
    {
        $allCourses = $this->collectPathCourses($academicYear, true);

        $enrollmentsByCourse = StudentCourseEnrollment::where('user_id', $userId)
            ->where('status', 'active')
            ->whereIn('advanced_course_id', $allCourses->pluck('id'))
            ->get()
            ->keyBy('advanced_course_id');

        $allCourses = $allCourses->map(function ($course) use ($enrollmentsByCourse) {
            $courseEnrollment = $enrollmentsByCourse->get($course->id);
            $course->is_enrolled = (bool) $courseEnrollment;
            $course->enrollment_progress = $courseEnrollment
                ? (float) ($courseEnrollment->progress ?? 0)
                : null;
            $course->is_completed = $course->is_enrolled && ($course->enrollment_progress ?? 0) >= 100;

            return $course;
        });

        $enrolledCourses = $allCourses->filter(fn ($course) => $course->is_enrolled);
        $completedCourses = $allCourses->filter(fn ($course) => $course->is_completed);
        $inProgressCourses = $enrolledCourses->filter(fn ($course) => !$course->is_completed);

        $progress = $allCourses->count() > 0
            ? round((float) $allCourses->avg(fn ($course) => (float) ($course->enrollment_progress ?? 0)), 1)
            : 0.0;

        $continueCourse = $inProgressCourses->sortByDesc(fn ($c) => (float) ($c->enrollment_progress ?? 0))->first()
            ?? $allCourses->first(fn ($c) => !$c->is_enrolled)
            ?? $allCourses->first();

        return [
            'courses' => $allCourses,
            'courses_count' => $allCourses->count(),
            'enrolled_courses_count' => $enrolledCourses->count(),
            'completed_courses_count' => $completedCourses->count(),
            'remaining_courses_count' => max(0, $allCourses->count() - $enrolledCourses->count()),
            'progress' => $progress,
            'continue_course' => $continueCourse,
        ];
    }

    /**
     * ملخص خفيف للبطاقة في قائمة المسارات
     */
    private function summarizePathCourses(AcademicYear $academicYear, int $userId): array
    {
        $allCourses = $this->collectPathCourses($academicYear, false);
        $ids = $allCourses->pluck('id');

        if ($ids->isEmpty()) {
            return [
                'courses_count' => 0,
                'enrolled_courses_count' => 0,
                'completed_courses_count' => 0,
                'remaining_courses_count' => 0,
                'progress' => 0.0,
            ];
        }

        $enrollments = StudentCourseEnrollment::where('user_id', $userId)
            ->where('status', 'active')
            ->whereIn('advanced_course_id', $ids)
            ->get()
            ->keyBy('advanced_course_id');

        $progressValues = $ids->map(function ($id) use ($enrollments) {
            $e = $enrollments->get($id);

            return $e ? (float) ($e->progress ?? 0) : 0.0;
        });

        $enrolledCount = $enrollments->count();
        $completedCount = $enrollments->filter(fn ($e) => (float) ($e->progress ?? 0) >= 100)->count();

        return [
            'courses_count' => $ids->count(),
            'enrolled_courses_count' => $enrolledCount,
            'completed_courses_count' => $completedCount,
            'remaining_courses_count' => max(0, $ids->count() - $enrolledCount),
            'progress' => round((float) $progressValues->avg(), 1),
        ];
    }

    private function collectPathCourses(AcademicYear $academicYear, bool $withRelations): Collection
    {
        $subjectIds = $academicYear->academicSubjects->pluck('id')->toArray();

        $fromSubjects = collect();
        if (!empty($subjectIds)) {
            $q = AdvancedCourse::where('is_active', true)->whereIn('academic_subject_id', $subjectIds);
            if ($withRelations) {
                $q->with(['academicSubject', 'academicYear', 'instructor'])->withCount('lessons');
            } else {
                $q->select('id');
            }
            $fromSubjects = $q->get();
        }

        if ($withRelations) {
            $linked = $academicYear->relationLoaded('linkedCourses')
                ? $academicYear->linkedCourses
                : $academicYear->linkedCourses()->where('is_active', true)
                    ->with(['academicSubject', 'academicYear', 'instructor'])
                    ->withCount('lessons')
                    ->get();
        } else {
            $linked = $academicYear->relationLoaded('linkedCourses')
                ? $academicYear->linkedCourses
                : $academicYear->linkedCourses()->where('is_active', true)->get(['advanced_courses.id']);
        }

        return $linked->merge($fromSubjects)->unique('id')->values();
    }
}

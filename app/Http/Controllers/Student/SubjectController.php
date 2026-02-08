<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SubjectController extends Controller
{
    /**
     * عرض الكورسات لمادة معينة
     */
    public function courses(AcademicSubject $academicSubject)
    {
        $allCourses = AdvancedCourse::where('is_active', true)
            ->with(['teacher'])
            ->withCount('lessons')
            ->select([
                'id',
                'title',
                'description',
                'category',
                'programming_language',
                'framework',
                'level',
                'duration_hours',
                'duration_minutes',
                'price',
                'is_free',
                'rating',
                'skills',
                'created_at',
                'thumbnail',
            ])
            ->get();

        $trackIdentifiers = collect([
            optional($academicSubject->academicYear)->code,
            optional($academicSubject->academicYear)->name,
            optional($academicSubject->academicYear)->description,
        ])->filter()->all();

        $trackCourses = $this->filterCourses($allCourses, $trackIdentifiers);
        if ($trackCourses->isEmpty()) {
            $trackCourses = $allCourses;
        }

        $subjectIdentifiers = [
            $academicSubject->code,
            $academicSubject->name,
            $academicSubject->description,
        ];

        $matchedCourses = $this->filterCourses($trackCourses, $subjectIdentifiers);
        if ($matchedCourses->isEmpty()) {
            $matchedCourses = $this->filterCourses($allCourses, $subjectIdentifiers);
        }

        $courses = $matchedCourses->map(function (AdvancedCourse $course) {
            $durationMinutes = ((int) ($course->duration_hours ?? 0) * 60) + (int) ($course->duration_minutes ?? 0);
            $course->setAttribute('duration_label', $this->formatDurationMinutes($durationMinutes));
            $course->setAttribute('tech_stack', collect($course->skills ?? [])->take(6));
            return $course;
        })->values();

        $courseStats = [
            'total' => $courses->count(),
            'languages' => $courses->pluck('programming_language')->filter()->unique()->values(),
            'frameworks' => $courses->pluck('framework')->filter()->unique()->values(),
            'levels' => $courses->pluck('level')->filter()->unique()->values(),
            'average_rating' => $courses->count() > 0 ? round((float) ($courses->avg('rating') ?? 0), 1) : null,
            'average_duration' => $this->formatDurationMinutes(
                $courses->count() > 0
                    ? (int) round($courses->sum(function ($course) {
                        return ((int) ($course->duration_hours ?? 0) * 60) + (int) ($course->duration_minutes ?? 0);
                    }) / $courses->count())
                    : 0
            ),
        ];

        return view('student.subjects.courses', [
            'academicSubject' => $academicSubject,
            'courses' => $courses,
            'courseStats' => $courseStats,
        ]);
    }

    private function filterCourses(Collection $courses, array $identifiers): Collection
    {
        $needles = collect($identifiers)
            ->filter()
            ->map(fn($value) => Str::of($value)->lower()->replace(['-', '_'], ' ')->squish())
            ->filter(fn($value) => $value->isNotEmpty());

        if ($needles->isEmpty()) {
            return collect();
        }

        return $courses->filter(function (AdvancedCourse $course) use ($needles) {
            $fields = collect([
                $course->category,
                $course->programming_language,
                $course->framework,
                $course->level,
                $course->title,
                $course->description,
            ])->merge((array) ($course->skills ?? []));

            return $fields->contains(function ($field) use ($needles) {
                if (empty($field)) {
                    return false;
                }

                $value = Str::of($field)->lower()->replace(['-', '_'], ' ')->squish();

                foreach ($needles as $needle) {
                    if ($needle->isNotEmpty() && Str::contains($value, $needle)) {
                        return true;
                    }
                }

                return false;
            });
        })->values();
    }

    private function formatDurationMinutes(int $minutes): ?string
    {
        if ($minutes <= 0) {
            return null;
        }

        $hours = intdiv($minutes, 60);
        $remaining = $minutes % 60;

        if ($hours === 0) {
            return $remaining . ' د';
        }

        if ($remaining === 0) {
            return $hours . ' س';
        }

        return $hours . ' س ' . $remaining . ' د';
    }
}
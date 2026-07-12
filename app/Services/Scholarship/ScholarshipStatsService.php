<?php

namespace App\Services\Scholarship;

use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CurriculumItem;
use App\Models\ScholarshipGroup;
use App\Models\ScholarshipProgram;
use App\Models\ScholarshipRegistration;
use App\Models\User;
use Illuminate\Support\Collection;

class ScholarshipStatsService
{
    public function overview(): array
    {
        $instructorIds = ScholarshipProgram::query()->distinct()->pluck('instructor_id')->filter();
        $scholarshipCourseIds = AdvancedCourse::query()
            ->where('is_scholarship_only', true)
            ->pluck('id');

        return [
            'programs_total' => ScholarshipProgram::count(),
            'programs_active' => ScholarshipProgram::where('is_active', true)->count(),
            'courses_total' => AdvancedCourse::where('is_scholarship_only', true)->count(),
            'courses_active' => AdvancedCourse::where('is_scholarship_only', true)->where('is_active', true)->count(),
            'instructors_total' => $instructorIds->count(),
            'registrations_total' => ScholarshipRegistration::count(),
            'registered' => ScholarshipRegistration::where('status', ScholarshipRegistration::STATUS_REGISTERED)->count(),
            'activated' => ScholarshipRegistration::where('status', ScholarshipRegistration::STATUS_ACTIVATED)->count(),
            'rejected' => ScholarshipRegistration::where('status', ScholarshipRegistration::STATUS_REJECTED)->count(),
            'deactivated' => ScholarshipRegistration::where('status', ScholarshipRegistration::STATUS_DEACTIVATED)->count(),
            'groups_total' => ScholarshipGroup::count(),
            'restricted_sections' => CourseSection::query()
                ->whereIn('advanced_course_id', $scholarshipCourseIds)
                ->whereIn('visibility_scope', ['selected', 'groups'])
                ->count(),
            'restricted_items' => CurriculumItem::query()
                ->whereHas('section', fn ($q) => $q->whereIn('advanced_course_id', $scholarshipCourseIds))
                ->whereIn('visibility_scope', ['selected', 'groups'])
                ->count(),
        ];
    }

    public function registrationStats(): array
    {
        return [
            'total' => ScholarshipRegistration::count(),
            'registered' => ScholarshipRegistration::where('status', ScholarshipRegistration::STATUS_REGISTERED)->count(),
            'activated' => ScholarshipRegistration::where('status', ScholarshipRegistration::STATUS_ACTIVATED)->count(),
            'rejected' => ScholarshipRegistration::where('status', ScholarshipRegistration::STATUS_REJECTED)->count(),
            'deactivated' => ScholarshipRegistration::where('status', ScholarshipRegistration::STATUS_DEACTIVATED)->count(),
        ];
    }

    public function recentPending(int $limit = 8): Collection
    {
        return ScholarshipRegistration::query()
            ->where('status', ScholarshipRegistration::STATUS_REGISTERED)
            ->with(['user', 'program'])
            ->orderByDesc('registered_at')
            ->limit($limit)
            ->get();
    }

    public function recentPrograms(int $limit = 6): Collection
    {
        return ScholarshipProgram::query()
            ->with(['instructor', 'course'])
            ->withCount([
                'registrations',
                'registrations as activated_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_ACTIVATED),
                'registrations as pending_count' => fn ($q) => $q->where('status', ScholarshipRegistration::STATUS_REGISTERED),
            ])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function instructorsWithStats(): Collection
    {
        $instructorIds = ScholarshipProgram::query()->distinct()->pluck('instructor_id')->filter();

        return User::query()
            ->whereIn('id', $instructorIds)
            ->orderBy('name')
            ->get()
            ->map(function (User $instructor) {
                $programIds = ScholarshipProgram::where('instructor_id', $instructor->id)->pluck('id');
                $instructor->setAttribute('programs_count', $programIds->count());
                $instructor->setAttribute('activated_students_count', ScholarshipRegistration::query()
                    ->whereIn('scholarship_program_id', $programIds)
                    ->where('status', ScholarshipRegistration::STATUS_ACTIVATED)
                    ->count());
                $instructor->setAttribute('pending_students_count', ScholarshipRegistration::query()
                    ->whereIn('scholarship_program_id', $programIds)
                    ->where('status', ScholarshipRegistration::STATUS_REGISTERED)
                    ->count());

                return $instructor;
            });
    }
}

<?php

namespace App\Observers;

use App\Models\StudentCourseEnrollment;
use App\Services\StatisticsCacheService;

class EnrollmentObserver
{
    /**
     * Handle the StudentCourseEnrollment "created" event.
     */
    public function created(StudentCourseEnrollment $enrollment): void
    {
        $this->clearCache();
    }

    /**
     * Handle the StudentCourseEnrollment "updated" event.
     */
    public function updated(StudentCourseEnrollment $enrollment): void
    {
        $this->clearCache();
    }

    /**
     * Handle the StudentCourseEnrollment "deleted" event.
     */
    public function deleted(StudentCourseEnrollment $enrollment): void
    {
        $this->clearCache();
    }

    /**
     * مسح الكاش
     */
    private function clearCache(): void
    {
        $statsService = app(StatisticsCacheService::class);
        $statsService->clearStats('enrollment_stats');
        $statsService->clearStats('dashboard_stats');
    }
}

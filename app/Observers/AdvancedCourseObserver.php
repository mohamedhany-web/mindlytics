<?php

namespace App\Observers;

use App\Models\AdvancedCourse;
use App\Models\ActivityLog;

class AdvancedCourseObserver
{
    /**
     * Handle the AdvancedCourse "created" event.
     */
    public function created(AdvancedCourse $advancedCourse): void
    {
        ActivityLog::logActivity(
            'course_created',
            $advancedCourse,
            null,
            $advancedCourse->only(['title', 'academic_subject_id', 'price', 'is_active'])
        );
    }

    /**
     * Handle the AdvancedCourse "updated" event.
     */
    public function updated(AdvancedCourse $advancedCourse): void
    {
        $changes = $advancedCourse->getChanges();
        
        if (!empty($changes)) {
            // تحديد نوع التحديث
            $action = 'course_updated';
            if (isset($changes['is_active'])) {
                $action = 'course_status_changed';
            } elseif (isset($changes['price'])) {
                $action = 'course_price_changed';
            }

            ActivityLog::logActivity(
                $action,
                $advancedCourse,
                $advancedCourse->getOriginal(),
                $changes
            );
        }
    }

    /**
     * Handle the AdvancedCourse "deleted" event.
     */
    public function deleted(AdvancedCourse $advancedCourse): void
    {
        ActivityLog::logActivity(
            'course_deleted',
            $advancedCourse,
            $advancedCourse->only(['title', 'academic_subject_id', 'price']),
            null
        );
    }

    /**
     * Handle the AdvancedCourse "restored" event.
     */
    public function restored(AdvancedCourse $advancedCourse): void
    {
        ActivityLog::logActivity(
            'course_restored',
            $advancedCourse,
            null,
            $advancedCourse->only(['title', 'academic_subject_id'])
        );
    }
}
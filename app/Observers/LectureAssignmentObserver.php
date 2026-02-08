<?php

namespace App\Observers;

use App\Models\LectureAssignment;
use App\Services\CalendarNotificationService;

class LectureAssignmentObserver
{
    protected $notificationService;

    public function __construct(CalendarNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the LectureAssignment "created" event.
     */
    public function created(LectureAssignment $assignment): void
    {
        // إنشاء إشعارات تلقائية عند إنشاء واجب محاضرة جديد
        if ($assignment->status === 'published') {
            $this->notificationService->createAssignmentNotifications($assignment);
        }
    }

    /**
     * Handle the LectureAssignment "updated" event.
     */
    public function updated(LectureAssignment $assignment): void
    {
        // إذا تم نشر الواجب، أنشئ إشعارات
        if ($assignment->wasChanged('status') && $assignment->status === 'published') {
            $this->notificationService->createAssignmentNotifications($assignment);
        }
        
        // إذا تم تغيير موعد التسليم، أنشئ إشعارات جديدة
        if ($assignment->wasChanged('due_date')) {
            $this->notificationService->createAssignmentNotifications($assignment);
        }
    }
}

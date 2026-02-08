<?php

namespace App\Observers;

use App\Models\Assignment;
use App\Services\CalendarNotificationService;

class AssignmentObserver
{
    protected $notificationService;

    public function __construct(CalendarNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Assignment "created" event.
     */
    public function created(Assignment $assignment): void
    {
        // إنشاء إشعارات تلقائية عند إنشاء واجب جديد
        if ($assignment->status === 'published') {
            $this->notificationService->createAssignmentNotifications($assignment);
        }
    }

    /**
     * Handle the Assignment "updated" event.
     */
    public function updated(Assignment $assignment): void
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

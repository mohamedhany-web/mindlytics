<?php

namespace App\Observers;

use App\Models\Exam;
use App\Services\CalendarNotificationService;

class ExamObserver
{
    protected $notificationService;

    public function __construct(CalendarNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Exam "created" event.
     */
    public function created(Exam $exam): void
    {
        // إنشاء إشعارات تلقائية عند إنشاء امتحان جديد
        if ($exam->is_active && $exam->is_published) {
            $this->notificationService->createExamNotifications($exam);
        }
    }

    /**
     * Handle the Exam "updated" event.
     */
    public function updated(Exam $exam): void
    {
        // إذا تم تفعيل أو نشر الامتحان، أنشئ إشعارات
        if ($exam->wasChanged(['is_active', 'is_published']) && $exam->is_active && $exam->is_published) {
            $this->notificationService->createExamNotifications($exam);
        }
        
        // إذا تم تغيير موعد الامتحان، أنشئ إشعارات جديدة
        if ($exam->wasChanged(['start_time', 'start_date'])) {
            $this->notificationService->createExamNotifications($exam);
        }
    }
}

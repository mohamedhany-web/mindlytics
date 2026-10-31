<?php

namespace App\Domains\Learning\Observers;

use App\Domains\Learning\Models\Lecture;
use App\Domains\Cms\Services\CalendarNotificationService;

class LectureObserver
{
    protected $notificationService;

    public function __construct(CalendarNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Lecture "created" event.
     */
    public function created(Lecture $lecture): void
    {
        // إنشاء إشعارات تلقائية عند إنشاء محاضرة جديدة
        $this->notificationService->createLectureNotifications($lecture);
    }

    /**
     * Handle the Lecture "updated" event.
     */
    public function updated(Lecture $lecture): void
    {
        // إذا تم تغيير موعد المحاضرة، أنشئ إشعارات جديدة
        if ($lecture->wasChanged('scheduled_at')) {
            $this->notificationService->createLectureNotifications($lecture);
        }
    }
}

<?php

namespace App\Observers;

use App\Models\ExamAttempt;
use App\Models\ActivityLog;

class ExamAttemptObserver
{
    /**
     * Handle the ExamAttempt "created" event.
     */
    public function created(ExamAttempt $examAttempt): void
    {
        try {
            $examTitle = 'غير معروف';
            if ($examAttempt->relationLoaded('exam')) {
                $examTitle = $examAttempt->exam->title ?? $examTitle;
            } else {
                $exam = $examAttempt->exam()->first();
                if ($exam) {
                    $examTitle = $exam->title ?? $examTitle;
                }
            }
            ActivityLog::logActivity(
                'exam_attempt_started',
                $examAttempt,
                null,
                [
                    'exam_id' => $examAttempt->exam_id,
                    'exam_title' => $examTitle,
                    'user_id' => $examAttempt->user_id,
                    'started_at' => $examAttempt->started_at,
                ]
            );
        } catch (\Throwable $e) {
            \Log::warning('ExamAttemptObserver::created failed (exam start not affected)', [
                'attempt_id' => $examAttempt->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the ExamAttempt "updated" event.
     */
    public function updated(ExamAttempt $examAttempt): void
    {
        $changes = $examAttempt->getChanges();
        
        if (!empty($changes)) {
            // تحديد نوع التحديث
            $action = 'exam_attempt_updated';
            
            if (isset($changes['status'])) {
                if ($changes['status'] === 'completed') {
                    $action = 'exam_attempt_submitted';
                    
                    // إرسال نتيجة الامتحان تلقائياً إذا كان مفعلاً
                    if (config('services.platform.auto_send_exam_results', true) && $examAttempt->exam->show_results_immediately) {
                        $this->sendExamResultNotification($examAttempt);
                    }
                } elseif ($changes['status'] === 'auto_submitted') {
                    $action = 'exam_attempt_auto_submitted';
                }
            } elseif (isset($changes['answers'])) {
                $action = 'exam_answer_saved';
            } elseif (isset($changes['tab_switches'])) {
                $action = 'exam_tab_switch';
            }

            ActivityLog::logActivity(
                $action,
                $examAttempt,
                $examAttempt->getOriginal(),
                $changes
            );
        }
    }

    /**
     * إرسال إشعار نتيجة الامتحان
     */
    private function sendExamResultNotification(ExamAttempt $examAttempt)
    {
        try {
            $whatsappService = app(\App\Services\WhatsAppService::class);
            
            // إرسال للطالب
            $whatsappService->sendExamResult($examAttempt->user, $examAttempt);
            
            // إرسال لولي الأمر إذا كان متاحاً
            if ($examAttempt->user->parent && $examAttempt->user->parent->phone) {
                $parentMessage = "📊 نتيجة امتحان جديدة لـ {$examAttempt->user->name}\n\n";
                $parentMessage .= "📝 الامتحان: {$examAttempt->exam->title}\n";
                $parentMessage .= "📊 النتيجة: {$examAttempt->score}/{$examAttempt->exam->total_marks} ({$examAttempt->percentage}%)\n";
                $parentMessage .= "✅ الحالة: {$examAttempt->result_status}\n";
                $parentMessage .= "📅 التاريخ: " . $examAttempt->submitted_at->format('d/m/Y H:i') . "\n\n";
                $parentMessage .= "📱 منصة مستر طارق الداجن";
                
                $whatsappService->sendMessage($examAttempt->user->parent->phone, $parentMessage);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send exam result notification', [
                'attempt_id' => $examAttempt->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the ExamAttempt "deleted" event.
     */
    public function deleted(ExamAttempt $examAttempt): void
    {
        ActivityLog::logActivity(
            'exam_attempt_deleted',
            $examAttempt,
            [
                'exam_id' => $examAttempt->exam_id,
                'user_id' => $examAttempt->user_id,
                'score' => $examAttempt->score,
                'status' => $examAttempt->status,
            ],
            null
        );
    }
}
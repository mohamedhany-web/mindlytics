<?php

namespace App\Services;

use App\Mail\EmployeeTaskAssignedMail;
use App\Models\EmployeeTask;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmployeeTaskAssignmentNotifier
{
    /**
     * إرسال بريد إلكتروني (SMTP / Gmail) عند إسناد مهمة لموظف.
     */
    public function notifyAssigned(EmployeeTask $task): void
    {
        $task->loadMissing(['employee', 'assigner']);
        $employee = $task->employee;
        if (! $employee || ! filter_var($employee->email, FILTER_VALIDATE_EMAIL)) {
            Log::info('employee_task_assigned_skip_mail', [
                'task_id' => $task->id,
                'reason' => 'no_valid_email',
            ]);

            return;
        }

        try {
            Mail::to($employee->email)->send(new EmployeeTaskAssignedMail($task));
        } catch (\Throwable $e) {
            Log::error('employee_task_assigned_mail_failed', [
                'task_id' => $task->id,
                'employee_id' => $employee->id,
                'mailer' => config('mail.default'),
                'message' => $e->getMessage(),
            ]);
            report($e);
        }
    }
}

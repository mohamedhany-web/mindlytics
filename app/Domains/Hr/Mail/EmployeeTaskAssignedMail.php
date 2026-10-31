<?php

namespace App\Domains\Hr\Mail;

use App\Domains\Hr\Models\EmployeeTask;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeTaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmployeeTask $task
    ) {
        $this->task->load(['employee', 'assigner']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم تعيين مهمة جديدة لك: ' . \Str::limit($this->task->title, 50),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'hr::emails.employee-task-assigned',
        );
    }
}

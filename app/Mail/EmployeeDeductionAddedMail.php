<?php

namespace App\Mail;

use App\Models\EmployeeSalaryDeduction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeDeductionAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmployeeSalaryDeduction $deduction
    ) {
        $this->deduction->load(['employee', 'agreement', 'creator']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'إشعار خصم من الراتب: ' . \Str::limit($this->deduction->title, 50),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.employee-deduction-added',
        );
    }
}

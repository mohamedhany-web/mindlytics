<?php

namespace App\Domains\Learning\Mail;

use App\Domains\Learning\Models\StudentCourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CourseEnrollmentActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public StudentCourseEnrollment $enrollment
    ) {
        $this->enrollment->loadMissing(['student', 'course']);
    }

    public function envelope(): Envelope
    {
        $courseTitle = optional($this->enrollment->course)->title ?? 'الكورس الخاص بك';

        return new Envelope(
            subject: 'تم تفعيل الكورس الخاص بك: ' . Str::limit($courseTitle, 60),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'learning::emails.course-enrollment-activated',
        );
    }
}


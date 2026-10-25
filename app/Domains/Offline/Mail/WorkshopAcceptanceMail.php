<?php

namespace App\Domains\Offline\Mail;

use App\Domains\Offline\Models\Workshop;
use App\Domains\Offline\Models\WorkshopRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WorkshopAcceptanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Workshop $workshop;

    public WorkshopRegistration $registration;

    public function __construct(Workshop $workshop, WorkshopRegistration $registration)
    {
        $this->workshop = $workshop;
        $this->registration = $registration;
    }

    public function build(): self
    {
        $subject = 'تأكيد حجزك في ورشة: ' . $this->workshop->title;

        $qrData = $this->registration->checkin_token;
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($qrData);

        return $this->subject($subject)
            ->view('offline::emails.workshop-acceptance', [
                'workshop' => $this->workshop,
                'registration' => $this->registration,
                'qrUrl' => $qrUrl,
            ]);
    }
}


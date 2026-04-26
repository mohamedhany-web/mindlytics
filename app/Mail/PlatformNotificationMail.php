<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlatformNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $titleText;
    public string $messageText;
    public ?string $actionUrl;
    public ?string $actionText;

    public function __construct(string $titleText, string $messageText, ?string $actionUrl = null, ?string $actionText = null)
    {
        $this->titleText = $titleText;
        $this->messageText = $messageText;
        $this->actionUrl = $actionUrl;
        $this->actionText = $actionText;
    }

    public function build()
    {
        return $this
            ->subject($this->titleText)
            ->view('emails.platform-notification');
    }
}


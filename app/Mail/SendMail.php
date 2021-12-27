<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($site, $title, $subtitle, $message)
    {
        $this->site = $site;

        $this->title = $title;

        $this->subtitle = $subtitle;

        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@' . $this->site, $this->title)
            ->subject($this->subtitle)
            ->html($this->message);
    }
}

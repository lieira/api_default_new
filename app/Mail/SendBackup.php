<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBackup extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $message, $backup)
    {
        $this->title = $title;
        $this->message = $message;
        $this->backup = $backup;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('BACKUP_EMAIL','teste@gmail.com'), env('APP_NAME','A CONFIGURAR'))
            ->subject($this->title)
            ->attach(storage_path($this->backup))
            ->html($this->message);

    }
}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $rem_data;

    /**
     * Create a new message instance.
     *
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->rem_data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reminder')
            ->with([
                'reminder' => $this->rem_data,
            ])
        ;
    }
}

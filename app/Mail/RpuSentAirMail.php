<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RpuSentAirMail extends Mailable
{
    use Queueable, SerializesModels;

    public $air_data;

    public function __construct($data)
    {
        $this->air_data = $data;
    }

    public function build()
    {
        $air_data = $this->air_data;
//        return $this->view('emails.event_creation', compact('air_data'));
    }
}

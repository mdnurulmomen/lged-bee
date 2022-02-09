<?php

namespace App\Jobs;

use App\Mail\SendMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $notification_data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification_data)
    {
        $this->notification_data = $notification_data;
    }

    public function handle()
    {
        $info = new SendMail($this->notification_data['body']);
        return Mail::to($this->notification_data['email'])->send($info);
    }
}

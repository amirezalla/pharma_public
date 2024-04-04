<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScheduledTaskCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $messageDetails;

    public function __construct($messageDetails)
    {
        $this->messageDetails = $messageDetails;
    }

    public function build()
    {
        return $this->view('mails.scheduledTaskCompleted')
                    ->with(['messageDetails' => $this->messageDetails]);
    }
}

<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalRejectionAddHotelEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $hotel;
    public $status;

    public function __construct($user, $hotel, $status)
    {
        $this->user = $user;
        $this->hotel = $hotel;
        $this->status = $status;
    }

    public function build()
    {
        $subject = __('Request to manage :hotel has been updated', ['hotel'=>$this->hotel]);

        return $this->subject($subject)->view('User::emails.approval-rejection-add-hotel', ['user'=>$this->user,'hotel'=>$this->hotel,'status'=>$this->status]);
    }


}

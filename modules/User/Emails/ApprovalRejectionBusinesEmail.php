<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalRejectionBusinesEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $business;

    public function __construct($user, $business)
    {
        $this->user = $user;
        $this->business = $business;
    }

    public function build()
    {
        $subject = __('Your :business_name Request Status', ['business_name'=>$this->user->business_name]);
        
        return $this->subject($subject)->view('User::emails.approval-rejection-business', ['user'=>$this->user,'business'=>$this->business]);
    }


}

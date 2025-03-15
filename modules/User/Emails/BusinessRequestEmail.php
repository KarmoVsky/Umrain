<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\User\Events\VendorApproved;

class BusinessRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $business;

    public function __construct($admin, $business)
    {
        $this->admin = $admin;
        $this->business = $business;
    }

    public function build()
    {
        $subject = __('New Business Profile');

        return $this->subject($subject)->view('User::emails.add-business-request',['admin'=>$this->admin, 'business'=>$this->business]);
    }


}

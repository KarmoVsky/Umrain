<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\User\Events\VendorApproved;

class UserInviteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $business;

    public function __construct($user, $business, $status)
    {
        $this->user = $user;
        $this->business = $business;
        $this->status = $status;
    }

    public function build()
    {
        $subject = $this->status === 'add'?
        __('Join :business_name workspace',['business_name'=>$this->business->business_name])
        :__('Cancel from :business_name workspace',['business_name'=>$this->business->business_name]);

        return $this->subject($subject)->view('User::emails.invite-user',['user'=>$this->user, 'business'=>$this->business, 'status'=>$this->status]);
    }


}

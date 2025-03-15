<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\RegisteredEmail;
use Modules\User\Emails\VendorApprovedEmail;
use Modules\User\Emails\UserInviteEmail;
use ModulerAdds\User\Events\SendMailUserRegistered;
use Modules\User\Events\VendorApproved;
use Modules\User\Events\UserInvite;
use Modules\User\Models\User;
use Modules\Vendor\Models\VendorRequest;

class SendUserInviteMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public $user;
    public $vendorRequest;

    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param Event $event
     * @return void
     */
    public function handle(UserInvite $event)
    {
        if($event->user->locale){
            $old = app()->getLocale();
            app()->setLocale($event->user->locale);
        }

        Mail::to($event->user->email)->send(new UserInviteEmail($event->user, $event->business, $event->status));

        if(!empty($old)){
            app()->setLocale($old);
        }

    }


}

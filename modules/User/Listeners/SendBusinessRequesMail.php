<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\RegisteredEmail;
use Modules\User\Emails\VendorApprovedEmail;
use Modules\User\Emails\BusinessRequestEmail;
use Modules\User\Emails\UserInviteEmail;
use ModulerAdds\User\Events\SendMailUserRegistered;
use Modules\User\Events\VendorApproved;
use Modules\User\Events\UserInvite;
use Modules\User\Events\AddBusinessRequest;
use Modules\User\Models\User;
use Modules\Vendor\Models\VendorRequest;

class SendBusinessRequesMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param Event $event
     * @return void
     */
    public function handle(AddBusinessRequest $event)
    {
        foreach($event->admins as $admin) {

            if($admin->locale){
                $old = app()->getLocale();
                app()->setLocale($admin->locale);
            }

            Mail::to($admin->email)->send(new BusinessRequestEmail($admin, $event->business));

            if(!empty($old)){
                app()->setLocale($old);
            }
        }
    }
}

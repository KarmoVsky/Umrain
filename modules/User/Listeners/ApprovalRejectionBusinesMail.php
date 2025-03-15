<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\ApprovalRejectionBusinesEmail;
use Modules\User\Events\ApprovalRejectionBusines;

class ApprovalRejectionBusinesMail
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
    public function handle(ApprovalRejectionBusines $event)
    {
        if($event->user->locale){
            $old = app()->getLocale();
            app()->setLocale($event->user->locale);
        }

        info($event->user->email);
        Mail::to($event->user->email)->send(new ApprovalRejectionBusinesEmail($event->user, $event->business));

        if(!empty($old)){
            app()->setLocale($old);
        }

    }


}

<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\ApprovalRejectionAddHotelEmail;
use Modules\User\Events\ApprovalRejectionAddHotel;

class ApprovalRejectionAddHotelMail
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
    public function handle(ApprovalRejectionAddHotel $event)
    {
        if($event->user->locale){
            $old = app()->getLocale();
            app()->setLocale($event->user->locale);
        }

        Mail::to($event->user->email)->send(new ApprovalRejectionAddHotelEmail($event->user, $event->hotel, $event->status));
        
        if(!empty($old)){
            app()->setLocale($old);
        }

    }


}

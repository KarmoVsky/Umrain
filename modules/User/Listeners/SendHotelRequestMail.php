<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Mail;
use Modules\User\Emails\AddHotelRequestEmail;
use Modules\User\Events\AddHotelRequest;

class SendHotelRequestMail
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
    public function handle(AddHotelRequest $event)
    {
        foreach($event->admins as $admin) {
            if($admin->locale){
                $old = app()->getLocale();
                app()->setLocale($admin->locale);
            }

            Mail::to($admin->email)->send(new AddHotelRequestEmail($admin, $event->business, $event->hotels));

            if(!empty($old)){
                app()->setLocale($old);
            }
        }

    }


}

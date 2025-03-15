<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Auth;
use App\Notifications\AdminChannelServices;
use App\Notifications\PrivateChannelServices;
use Modules\User\Events\AddBusinessRequest;

class NotifyAdminsOfVendorRequestListener
{

    public function handle(AddBusinessRequest $event)
    {
        $user = $event->business->user;
        $message = __(':name request to be a vendor', ['name'=>$user->first_name.' '.$user->last_name]);
        $data = [
            'id'      => 1,
            'event'   => 'AddBusinessRequest',
            'to'      => 'admin',
            'name'    => Auth::user()->display_name,
            'avatar'  => Auth::user()->avatar_url,
            'link'    => get_link_detail_services('user', $event->business->id, 'business.getById'),//result of this function is: route('user.admin.business.getById', ['id'=>$event->business->id])
            'type'    => 'event->type',
            'message' => $message
        ];
        // notify to admin
        Auth::user()->notify(new AdminChannelServices($data));

    }


}

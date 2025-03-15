<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Auth;
use App\Notifications\AdminChannelServices;
use App\Notifications\PrivateChannelServices;
use Modules\User\Events\ApprovalRejectionAddHotel;

class NotifyVendorOfApprovalRejectionAddHotelListener
{
    public function handle(ApprovalRejectionAddHotel $event)
    {
        $user = $event->user;
        $message = $event->status === 'approved' ?
         __("Your request to manage :hotel has been accepted and you can now manage the service", ['hotel'=>$event->hotel])
        : __("Your request to manage :hotel has been rejected and you can't manage the service", ['hotel'=>$event->hotel]);
        $route = $event->status === 'approved' ? route('hotel.vendor.index') : '';//Must check the permissions link if the vendors can access the hotel management
        $data = [
            'id'      => 1,
            'event'   => 'ApprovalRejectionAddHotel',
            'to'      => 'vendor',
            'name'    => Auth::user()->display_name,
            'avatar'  => Auth::user()->avatar_url,
            'link'    => $route,
            'type'    => 'event->type',
            'message' => $message
        ];
        // notify to vendor
        $user->notify(new PrivateChannelServices($data));

    }
}

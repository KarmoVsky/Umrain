<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Auth;
use App\Notifications\AdminChannelServices;
use App\Notifications\PrivateChannelServices;
use Modules\User\Events\ApprovalRejectionBusines;

class NotifyVendorOfApprovalRejectionListener
{
    public function handle(ApprovalRejectionBusines $event)
    {
        $user = $event->user;
        $message = $event->business->status === 'approved' ?
        __('Your request to create :business_name has been accepted and you can now manage the services', ['business_name'=>$event->business->business_name])
        : __('Your request to create :business_name has been rejected and you can now manage the services', ['business_name'=>$event->business->business_name]);
        $route = $event->business->status === 'approved' ? route('vendor.dashboard') : '';
        $data = [
            'id'      => 1,
            'event'   => 'ApprovalRejectionBusines',
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

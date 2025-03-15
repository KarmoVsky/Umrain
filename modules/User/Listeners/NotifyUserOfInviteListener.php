<?php

namespace Modules\User\Listeners;

use Illuminate\Support\Facades\Auth;
use App\Notifications\AdminChannelServices;
use App\Notifications\PrivateChannelServices;
use Modules\User\Events\UserInvite;

class NotifyUserOfInviteListener
{
    public function handle(UserInvite $event) {
        $user = $event->user;
        $message = $event->status === 'add' ?
        __('You have been added to :business_name workspace and you can manage them services', ['business_name'=>$event->business->business_name])
        : __("You have been removed from :business_name workspace and you don't have access any more", ['business_name'=>$event->business->business_name]);
        $route = $event->status === 'add' ? route('user.profile.index') : '';
        $data = [
            'id'      => 1,
            'event'   => 'UserInvite',
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

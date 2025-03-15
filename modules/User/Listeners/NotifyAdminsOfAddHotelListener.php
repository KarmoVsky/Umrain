<?php

namespace Modules\User\Listeners;

use Modules\User\Events\AddHotelRequest;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AdminChannelServices;
use App\Notifications\PrivateChannelServices;

class NotifyAdminsOfAddHotelListener {

    public function handle(AddHotelRequest $event) {
        $business = $event->business;
        $hotels = $event->hotels;
        foreach($hotels as $hotel) {
            $message = __(':business_name request to be add hotel :hotel',
            ['business_name'=>$business->business_name,'hotel'=>$hotel['title']]);
            $data = [
                'id'      => 1,
                'event'   => 'AddHotelRequest',
                'to'      => 'admin',
                'name'    => Auth::user()->display_name,
                'avatar'  => Auth::user()->avatar_url,
                'link'    => get_link_detail_services('hotel', $hotel['id'], 'edit'),//result of this function is: route('hotel.admin.edit',['id'=>$hotel['id']])
                'type'    => 'event->type',
                'message' => $message
            ];
            // notify to admin
            Auth::user()->notify(new AdminChannelServices($data));
        }
    }
}

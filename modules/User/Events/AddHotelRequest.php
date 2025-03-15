<?php
namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;

class  AddHotelRequest
{
    use SerializesModels;
    public $admins;
    public $business;
    public $hotels;

    public function __construct($admins, $business, $hotels = null)
    {
        $this->admins = $admins;
        $this->business = $business;
        $this->hotels = $hotels;
    }
}

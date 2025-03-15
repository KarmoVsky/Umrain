<?php
namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;

class  AddBusinessRequest
{
    use SerializesModels;
    public $admins;
    public $business;

    public function __construct($admins, $business)
    {
        $this->admins = $admins;
        $this->business = $business;
    }
}

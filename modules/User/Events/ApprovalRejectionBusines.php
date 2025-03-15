<?php
namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;

class  ApprovalRejectionBusines
{
    use SerializesModels;
    public $user;
    public $business;

    public function __construct($user, $business)
    {
        $this->user = $user;
        $this->business = $business;
    }
}

<?php

namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;

class  UserInvite
{
    use SerializesModels;
    public $user;
    public $business;
    public $status;

    public function __construct($user,$business,$status)
    {
        $this->user = $user;
        $this->business = $business;
        $this->status = $status;
    }
}

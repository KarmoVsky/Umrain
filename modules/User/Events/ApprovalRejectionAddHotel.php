<?php
namespace Modules\User\Events;

use Illuminate\Queue\SerializesModels;

class  ApprovalRejectionAddHotel
{
    use SerializesModels;
    public $user;
    public $hotel;
    public $status;

    public function __construct($user, $hotel, $status)
    {
        $this->user = $user;
        $this->hotel = $hotel;
        $this->status = $status;
    }
}

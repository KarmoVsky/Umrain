<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddHotelRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $business;
    public $services;

    public function __construct($admin, $business, $services)
    {
        $this->admin = $admin;
        $this->business = $business;
        $this->services = $services;
    }

    public function build()
    {
        $subject = __('New request to manage service');

        return $this->subject($subject)->view('User::emails.add-hotel-request',['admin'=>$this->admin, 'business'=>$this->business,'services'=>$this->services]);
    }


}

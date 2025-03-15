<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Booking\Events\EnquirySendEvent;
use Modules\Booking\Listeners\EnquiryNotifyListen;
use Modules\Booking\Listeners\EnquirySendListen;
use Modules\User\Events\NewVendorRegistered;
use Modules\User\Events\SendMailUserRegistered;
use Modules\User\Events\VendorApproved;
use Modules\User\Listeners\SendMailUserRegisteredListen;
use Modules\User\Listeners\SendNotifyApproved;
use Modules\User\Listeners\SendNotifyRegistered;
use Modules\User\Listeners\SendNotifyRegisteredListen;
use Modules\User\Listeners\SendVendorApprovedMail;
use Modules\User\Events\UserInvite;
use Modules\User\Events\AddBusinessRequest;
use Modules\User\Events\AddHotelRequest;
use Modules\User\Events\ApprovalRejectionBusines;
use Modules\User\Events\ApprovalRejectionAddHotel;
use Modules\User\Listeners\SendUserInviteMail;
use Modules\User\Listeners\SendHotelRequestMail;
use Modules\User\Listeners\NotifyUserOfInviteListener;
use Modules\User\Listeners\SendBusinessRequesMail;
use Modules\User\Listeners\ApprovalRejectionBusinesMail;
use Modules\User\Listeners\ApprovalRejectionAddHotelMail;
use Modules\User\Listeners\SendVendorRegisterdEmail;
use Modules\User\Listeners\NotifyAdminsOfVendorRequestListener;
use Modules\User\Listeners\NotifyAdminsOfAddHotelListener;
use Modules\User\Listeners\NotifyVendorOfApprovalRejectionListener;
use Modules\User\Listeners\NotifyVendorOfApprovalRejectionAddHotelListener;
use Modules\Vendor\Events\PayoutRequestEvent;
use Modules\Vendor\Listeners\PayoutNotifyListener;
use Modules\Vendor\Listeners\PayoutRequestNotificationListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SendMailUserRegistered::class => [
            SendMailUserRegisteredListen::class,
            SendNotifyRegisteredListen::class
        ],
        VendorApproved::class=>[
            SendVendorApprovedMail::class,
            SendNotifyApproved::class
        ],
        UserInvite::class => [
            SendUserInviteMail::class,
            NotifyUserOfInviteListener::class
        ],
        AddBusinessRequest::class => [
            SendBusinessRequesMail::class,
            NotifyAdminsOfVendorRequestListener::class
        ],
        AddHotelRequest::class => [
            SendHotelRequestMail::class,
            NotifyAdminsOfAddHotelListener::class
        ],
        ApprovalRejectionAddHotel::class => [
            ApprovalRejectionAddHotelMail::class,
            NotifyVendorOfApprovalRejectionAddHotelListener::class

        ],
        ApprovalRejectionBusines::class => [
            ApprovalRejectionBusinesMail::class,
            NotifyVendorOfApprovalRejectionListener::class
        ],
        NewVendorRegistered::class=>[
            SendVendorRegisterdEmail::class,
            SendNotifyRegistered::class
        ],
//        VendorLogPayment::class =>[
//            VendorLogPaymentListen::class
//        ]
        PayoutRequestEvent::class=>[
            PayoutRequestNotificationListener::class,
            PayoutNotifyListener::class
        ],
        EnquirySendEvent::class=>[
            EnquirySendListen::class,
            EnquiryNotifyListen::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}

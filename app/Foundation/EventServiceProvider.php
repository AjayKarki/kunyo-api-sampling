<?php

namespace Foundation;

use Foundation\Events\VerifyUser;
use Foundation\Events\UserCreated;
use Foundation\Events\OrderPending;
use Foundation\Events\OrderDelivered;
use Foundation\Listeners\TicketCreated;
use Foundation\Listeners\WelcomeEmail;
use Foundation\Events\TicketCreated as TicketCreatedEvent;
use Foundation\Listeners\TicketCreated as TicketCreatedListener;
use Foundation\Events\TicketActivity as TicketActivityEvent;
use Foundation\Listeners\TicketActivity as TicketActivityListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package Foundation
 */
class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserCreated::class => [
            WelcomeEmail::class,
        ],
        OrderPending::class => [
            Listeners\OrderPending::class,
        ],
        OrderDelivered::class => [
            Listeners\OrderDelivered::class,
        ],
        VerifyUser::class => [
            Listeners\VerifyUser::class,
        ],
        TicketCreatedEvent::class => [
            TicketCreatedListener::class
        ],
        TicketActivityEvent::class => [
            TicketActivityListener::class
        ],
        Events\KYCRespond::class => [
            Listeners\KYCVerified::class,
            Listeners\KYCRejected::class
        ],
        Events\DiscountVoucherCreated::class => [
            Listeners\DiscountVoucherCreated::class
        ],
        Events\EmailCampaignCreated::class => [
            Listeners\EmailCampaignCreated::class
        ],
        Events\PlayerInfoRequest::class => [
            Listeners\PlayerInfoRequest::class
        ]
    ];

}

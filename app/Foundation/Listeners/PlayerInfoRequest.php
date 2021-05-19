<?php

namespace Foundation\Listeners;

use Foundation\Events\PlayerInfoRequest as Event;
use Foundation\Lib\TicketEvent;
use Foundation\Lib\TicketEvent as TicketEventLib;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Email\Libs\Mailer;

class PlayerInfoRequest implements ShouldQueue
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Create the event listener.
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param $event
     * @return void
     */
    public function handle(Event $event)
    {
        $topup = $event->topup;
        $amount = $topup->amounts[0];
        $user = $event->user;

        $fields = [
            '{TOPUP_CUSTOMER_NAME}' => $user->getFullName(),
            '{TOPUP_NAME}' => $topup->name,
            '{TOPUP_LINK}' => '<a href="' . route('single', ['top-up', $topup->slug]) .'" target="_blank">' . $topup->name . ' </a>',
            '{TOPUP_AMOUNT_TITLE}' => $amount->title,
            '{TOPUP_AMOUNT_PRICE}' => $amount->price,
            '{RESUBMIT_LINK}' => '<a href="' . route('user.player-info.submit', $event->orderId) .'" target="_blank"> Resubmit Information </a>'
        ];

        try {
            $this->mailer
                ->setSubject('Kunyo.co | Resubmit your Player Information')
                ->setView('email.template')
                ->send('topup_player_info_request', $fields, [
                    'receiver' => $user->email,
                    'sender'   => config('mail.from.address'),
                ]);
        } catch (\Exception $exception) {
            \Log::debug($exception->getMessage(), [
                'subject'  => 'Kunyo.co | Resubmit your Player Information',
                'receiver' => $user->email,
                'sender'   => config('mail.from.address'),
            ]);
        }
    }
}

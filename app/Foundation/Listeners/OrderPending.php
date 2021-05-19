<?php

namespace Foundation\Listeners;

use Illuminate\Bus\Queueable;
use Modules\Email\Libs\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Foundation\Resolvers\EmailOrderResolver;
use Foundation\Events\OrderPending as Pending;
use Foundation\Resolvers\Exception\EmailAddress;

/**
 * Class OrderPending
 * @package Foundation\Listeners
 */
class OrderPending implements ShouldQueue
{

    use Queueable, SerializesModels;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Create the event listener.
     *
     * @param Mailer $mailer
     */
    public function __construct(
        Mailer $mailer
    )
    {
        $this->mailer   = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param Pending $event
     * @return void
     * @throws \Throwable
     */
    public function handle(Pending $event)
    {
        try {
            $this->mailer
                ->setSubject('Kunyo - Order Pending')
                ->setView('email.template')
                ->send('order_pending_transaction_template', [
                    '{APP_NAME}'  => config('app.name'),
                    '{WEB_LINK}'  => url('/'),
                    '{SITE_URL}'  => url('/'),
                    '{USER_NAME}' => $event->receiverFullName,
                    '{ORDER_VIEW}' => EmailOrderResolver::resolvePendingOrder($event->payment),
                    '{ORDER_DATE}' => $event->payment->created_at->format('Y-m-d'),
                    '{ORDER_STATUS}' => $event->payment->orders->where('delivery_status', '!=', \Foundation\Lib\Order::ORDER_COMPLETED_STATUS)->count() ? 'Processing' : 'Completed',
                    '{ORDER_ID}' => $event->payment->transaction_id,
                ], [
                    'receiver' => $event->receiverEmail,
                    'sender'   => config('mail.from.address'),
                ]);
        } catch (\Exception $exception) {

            if (EmailAddress::isInvalid($exception)) {
                \Foundation\Resolvers\Otp\VerifyUser::deactivateUser(optional($event->payment)->user_id);
            }

            \Log::debug($exception->getMessage(), [
                'subject'  => 'Kunyo - Order Pending',
                'receiver' => $event->receiverEmail,
            ]);
        }
    }

}

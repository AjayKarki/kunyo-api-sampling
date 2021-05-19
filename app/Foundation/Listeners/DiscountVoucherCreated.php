<?php

namespace Foundation\Listeners;

use App\Foundation\Lib\DiscountVoucher;
use Foundation\Events\DiscountVoucherCreated as Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Email\Libs\Mailer;

class DiscountVoucherCreated
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
     * @param  Event  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $voucher = $event->voucher->load('customer:id,first_name,middle_name,last_name,email');
        $fields = [
            '{CUSTOMER_NAME}' => $voucher->customer->getFullName(),
            '{VOUCHER_NAME}' => $voucher->name,
            '{VOUCHER_CODE}' =>  $voucher->voucher,
            '{VOUCHER_START_DATE}' =>  $voucher->start_date->format('M d, Y H:i A'),
            '{VOUCHER_END_DATE}' =>  $voucher->end_date->format('M d, Y H:i A'),
            '{VOUCHER_DISCOUNT_AMOUNT}' => $voucher->type == DiscountVoucher::TYPE_AMOUNT ? ('Rs. ' . $event->voucher->discount_amount) : ($event->voucher->discount_percent . ' %'),
            '{VOUCHER_USAGE_LIMIT}' => $voucher->max_use,
            '{VOUCHER_MIN_ORDER}' => 'Rs. ' . $voucher->min_order_amount
        ];


        try {
            $this->mailer
                ->setSubject('Kunyo.co | You have received a Discount !')
                ->setView('email.template')
                ->send('discount_voucher_assigned', $fields, [
                    'receiver' => $voucher->customer->email,
                    'sender'   => config('mail.from.address'),
                ]);
        } catch (\Exception $exception) {
            \Log::debug($exception->getMessage(), [
                'subject'  => 'Kunyo.co | You have received a Discount !',
                'receiver' => $voucher->customer->email,
                'sender'   => config('mail.from.address'),
            ]);
        }
    }
}

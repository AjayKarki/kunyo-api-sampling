<?php

namespace Foundation\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Payment\Payment;

/**
 * Class OrderPending
 * @package Foundation\Events
 */
final class OrderPending
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;

    public $receiverEmail;

    public $receiverFullName;

    /**
     * Create a new event instance.
     *
     * @param Payment $payment
     * @param $receiverEmail
     * @param $receiverFullName
     */
    public function __construct(Payment $payment, $receiverEmail, $receiverFullName)
    {
        $this->payment  = $payment;
        $this->receiverEmail = $receiverEmail;
        $this->receiverFullName = $receiverFullName;
    }

}

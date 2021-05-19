<?php

namespace Foundation\Events;

use Foundation\Models\DiscountVoucher;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscountVoucherCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var DiscountVoucher
     */
    public $voucher;

    /**
     * Create a new event instance.
     *
     * @param DiscountVoucher $discountVoucher
     */
    public function __construct(DiscountVoucher $discountVoucher)
    {
        $this->voucher = $discountVoucher;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

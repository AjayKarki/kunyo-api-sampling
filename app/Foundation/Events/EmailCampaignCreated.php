<?php

namespace Foundation\Events;

use Foundation\Models\EmailCampaign;
use Foundation\Models\TicketEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailCampaignCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var EmailCampaign
     */
    public $campaign;

    /**
     * Create a new event instance.
     *
     * @param EmailCampaign $emailCampaign
     */
    public function __construct(EmailCampaign $emailCampaign)
    {
        $this->campaign = $emailCampaign;
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

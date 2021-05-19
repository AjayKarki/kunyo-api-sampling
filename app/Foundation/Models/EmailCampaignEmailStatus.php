<?php


namespace App\Foundation\Models;

use Neputer\Supports\BaseModel as Model;

class EmailCampaignEmailStatus extends Model
{
    protected $table = 'email_campaign_email_status';

    protected $fillable = [
        'email_sent',
        'email_sent_at',
        'email_delivered',
        'email_delivered_at',
        'email_opened',
        'email_opened_at',
        'email_bounced',
        'hard_bounced',
    ];

    protected $dates = [
        'email_sent_at',
        'email_delivered_at',
        'email_opened_at',
    ];

}

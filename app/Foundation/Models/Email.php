<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Email
 * @package Foundation\Models
 */
class Email extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'from_address',
        'content',
        'email_campaign_id',
        'email_markup',
        'attachments',
        'banner_image'
    ];

    /**
     * The campaign that this email belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emailCampaign()
    {
        return $this->belongsTo(EmailCampaign::class);
    }

}

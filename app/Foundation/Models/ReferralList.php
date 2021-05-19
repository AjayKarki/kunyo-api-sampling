<?php

namespace Foundation\Models;

use Modules\Payment\Payment;
use Neputer\Supports\BaseModel as Model;

/**
 * Class Referral
 * @package Foundation\Models
 */
class ReferralList extends Model
{
    protected $table = 'referral_list';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'referer_id',
        'referral_id',
        'user_id',
        'user_name',
        'status',
        'is_used',
    ];

    /**
     * Get referral link of this referral
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    /**
     * The person creating the referral link
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referer()
    {
        return $this->belongsTo(User::class, 'referer_id');
    }

    /**
     * User who registered through the link
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Payment::class, 'user_id', 'transaction_id', 'user_id', 'id');
    }
}

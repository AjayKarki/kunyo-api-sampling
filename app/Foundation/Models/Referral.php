<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Referral
 * @package Foundation\Models
 */
class Referral extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'link',
        'code',
        'user_id',
        'user_name',
        'status',
    ];

    /**
     * User who created the referral link
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * List of referrals through this link
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referralList()
    {
        return $this->hasMany(ReferralList::class, 'referral_id');
    }

}

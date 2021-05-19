<?php

namespace Foundation\Models;

use Carbon\Carbon;
use Neputer\Supports\BaseModel as Model;

/**
 * Class DiscountVoucher
 * @package Foundation\Models
 */
class DiscountVoucher extends Model
{

    protected $casts = [
        'start_date' => 'datetime:Y-m-d\TH:i',
        'end_date' => 'datetime:Y-m-d\TH:i'
    ];

    public function setVoucherAttribute($value){
        $this->attributes['voucher'] = strtoupper($value);
    }

    public function setStartDateAttribute($value){
        $this->attributes['start_date'] = Carbon::parse($value);
    }

    public function setEndDateAttribute($value){
        $this->attributes['end_date'] = Carbon::parse($value);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'voucher',
        'start_date',
        'end_date',
        'type',
        'discount_amount',
        'discount_percent',
        'max_use',
        'use_count',
        'min_order_amount',
        'status',
        'use_type',
        'user_id',
    ];

    /**
     * Customer who can use this voucher
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}

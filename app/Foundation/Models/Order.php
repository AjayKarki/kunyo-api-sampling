<?php

namespace Foundation\Models;

use Modules\Payment\Payment;
use Neputer\Supports\BaseModel;
use Neputer\Supports\Mixins\usesUuid;

/**
 * Class Order
 * @package Foundation\Models
 */
class Order extends BaseModel
{

    use usesUuid;

    protected $fillable = [
        'transaction_id',
        'order_id',
        'order_type_id',
        'order_type',
        'quantity',
        'amount',
        'discount',
        'discounted_amount',
        'status',
        'remarks',
        'delivery_status',
        'delivered_at',
        'assigned_to',
        'metas',
        'assigned_order_item',
        'original_price',
        'selling_price'
    ];

    protected $dates = [ 'delivered_at', ];

    protected $casts = [
        'metas' => 'array',
        'assigned_order_item' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Payment::class,  'transaction_id', 'id');
    }

    public function getUuidKey()
    {
        return 'order_id';
    }

    /**
     * Player Information for Topup
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playerInfo()
    {
        return $this->hasMany(TopupPlayerInformation::class);
    }

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class, 'order_type_id');
    }

    public function topup()
    {
        return $this->belongsTo(TopUp::class, 'order_type_id');
    }
}

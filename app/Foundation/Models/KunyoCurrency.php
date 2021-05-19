<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;
use Neputer\Supports\Mixins\usesUuid;

/**
 * Class KunyoCurrency
 * @package Foundation\Models
 */
class KunyoCurrency extends Model
{
    use usesUuid;

    protected $table = 'kunyo_currency';

    protected $casts = [
        'metas' => 'array',
    ];

    protected $dates = ['delivered_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'user_name',
        'quantity',
        'rate',
        'amount',
        'service_charge',
        'delivery_status',
        'delivered_at',
        'payment_gateway_id',
        'payment_status',
        'metas',
        'remarks',
    ];

    /**
     * Get UUID Key Column Name
     *
     * @return string
     */
    public function getUuidKey()
    {
        return 'order_id';
    }

    /**
     * Get UUID value
     *
     * @return mixed
     */
    public function getUuid()
    {
        return $this->order_id;
    }

}

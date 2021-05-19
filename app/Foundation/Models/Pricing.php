<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Pricing
 * @package Foundation\Models
 */
class Pricing extends Model
{
    protected $table = 'pricing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'price',
        'country',
        'priceable_type',
        'priceable_id',
        'status'
    ];

    /**
     * The region the price belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo(PaymentRegion::class, 'country');
    }

}

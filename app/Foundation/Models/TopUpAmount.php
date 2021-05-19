<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class TopUpAmount
 * @package Foundation\Models
 */
class TopUpAmount extends Model
{

    protected $table = 'game_top_ups_amounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_top_ups_id', 'title', 'price', 'status', 'original_price'
    ];

    public function prices()
    {
        return $this->morphMany(Pricing::class, 'priceable');
    }

}

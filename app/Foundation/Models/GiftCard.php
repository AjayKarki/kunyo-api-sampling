<?php

namespace Foundation\Models;

use Foundation\Mixins\Revisionable;
use Neputer\Supports\BaseModel as Model;

/**
 * Class GiftCard
 * @package Foundation\Models
 */
class GiftCard extends Model
{

    use Revisionable;

    protected $table = 'gift_cards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'publisher_id', 'developer_id', 'genre_id', 'platform_id', 'delivery_mode_id',
        'delivery_time_id', 'out_of_stock_limit', 'can_purchase',
        'region_id', 'name', 'slug', 'supported_games', 'image', 'price', 'description', 'status', 'created_by',
        'seo_title', 'seo_description', 'metas', 'terms_and_conditions', 'is_shown', 'is_forced_to_confirm',
        'is_order_disable_reason', 'is_order_disable', 'original_price',
    ];

    protected $casts = [
        'metas' => 'array',
    ];

    public function codes()
    {
        return $this->hasMany(GiftCardsCode::class, 'gift_cards_id', 'id');
    }

    public function active_codes()
    {
        return $this->hasMany(GiftCardsCode::class, 'gift_cards_id', 'id')
            ->where('is_used', 0);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function prices()
    {
        return $this->morphMany(Pricing::class, 'priceable');
    }

}

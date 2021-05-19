<?php

namespace Foundation\Models;

use Foundation\Mixins\Revisionable;
use Neputer\Supports\BaseModel as Model;
use phpDocumentor\Reflection\Types\Self_;

/**
 * Class TopUp
 * @package Foundation\Models
 */
class TopUp extends Model
{

    use Revisionable;

    protected $table = 'game_top_ups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'publisher_id', 'developer_id', 'genre_id', 'platform_id', 'delivery_mode_id', 'delivery_time_id', 'region_id', 'category_id', 'name', 'slug', 'supported_games', 'image', 'description', 'status', 'created_by',
        'seo_title', 'seo_description', 'metas', 'priority', 'terms_and_conditions', 'is_shown', 'is_forced_to_confirm',
        'is_order_disable_reason', 'is_order_disable',
    ];

    protected $casts = [
        'metas' => 'array',
    ];

    public function amounts()
    {
        return $this->hasMany(TopUpAmount::class, 'game_top_ups_id', 'id');
    }

    public function attributes()
    {
        return $this->hasMany(TopUpAttribute::class, 'game_top_ups_id', 'id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}

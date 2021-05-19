<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Category
 * @package Foundation\Models
 */
class Category extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'category_name', 'created_by', 'slug', 'description', 'image', 'status', 'type', 'is_shown', 'priority',
        'seo_title', 'seo_description',
    ];

    public function parent(){
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','created_by');
    }

    public function top_ups()
    {
        return $this->hasMany(TopUp::class);
    }

    public function gift_cards()
    {
        return $this->hasMany(GiftCard::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'category_collections', 'category_id', 'collection_id');
    }

}

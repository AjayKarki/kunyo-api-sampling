<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Post
 * @package Foundation\Models
 */
class Post extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title','slug', 'content', 'post_type', 'category_id', 'sub_category_id', 'views', 'image', 'status', 'created_by', 'seo_title', 'seo_slug', 'seo_desc', 'seo_keywords'
    ];

    public function tags()
    {
        return $this->morphToMany(Tag::class,'taggable');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','created_by');
    }

}

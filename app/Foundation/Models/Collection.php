<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Collection
 * @package Foundation\Models
 */
class Collection extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description', 'status'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_collections', 'collection_id', 'category_id');
    }

}

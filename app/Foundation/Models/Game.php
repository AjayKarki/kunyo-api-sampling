<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Email
 * @package Foundation\Models
 */
class Game extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'content', 'image', 'category_id', 'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


}

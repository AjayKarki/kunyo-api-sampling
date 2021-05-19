<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Banner
 * @package Foundation\Models
 */
class Banner extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'caption', 'status', 'rank', 'image', 'url', 'open_in',
    ];

}

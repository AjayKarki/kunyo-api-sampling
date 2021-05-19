<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Publisher
 * @package Foundation\Models
 */
class Region extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'status'
    ];

}

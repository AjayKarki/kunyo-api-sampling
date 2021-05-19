<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Platform
 * @package Foundation\Models
 */
class Platform extends Model
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

<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Developer
 * @package Foundation\Models
 */
class Developer extends Model
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

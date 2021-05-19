<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Testimonial
 * @package Foundation\Models
 */
class Testimonial extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'designations', 'rating', 'image', 'description', 'status'
    ];

}

<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Accounting
 * @package Foundation\Models
 */
class Accounting extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description'
    ];

}

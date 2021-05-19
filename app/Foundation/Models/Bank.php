<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Bank
 * @package Foundation\Models
 */
class Bank extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'branch', 'account_name', 'account_number', 'currency', 'description', 'status'
    ];

}

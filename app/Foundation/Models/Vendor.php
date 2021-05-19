<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Vendor
 * @package Foundation\Models
 */
class Vendor extends Model
{
    protected $casts = [
        'pos' => 'array',
        'cheque' => 'array',
        'cash' => 'array',
        'bank_transfer' => 'array',
        'online_transfer' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
        'contact_person',
        'contact_person_phone',
        'pos',
        'cheque',
        'cash',
        'bank_transfer',
        'online_transfer',
    ];

}

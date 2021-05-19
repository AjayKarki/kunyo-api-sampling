<?php

namespace Foundation\Models;

use Illuminate\Support\Str;
use Neputer\Supports\BaseModel as Model;

/**
 * Class AccountingCategory
 * @package Foundation\Models
 */
class AccountingCategory extends Model
{
    protected $casts = [
        'type' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'type',
    ];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

}

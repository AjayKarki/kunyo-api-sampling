<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class TopUpAttribute
 * @package Foundation\Models
 */
class TopUpAttribute extends Model
{
    protected $table = 'game_top_ups_attributes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_top_ups_id', 'title', 'placeholder', 'required', 'status',
    ];

}

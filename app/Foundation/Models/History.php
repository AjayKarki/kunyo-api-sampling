<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class Genre
 * @package Foundation\Models
 */
class History extends Model
{
    protected $table = 'history';

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'meta' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'information', 'user_id', 'user_name', 'historyable_type', 'historyable_id', 'old_value', 'new_value', 'meta', 'type', 'updated_at',
    ];

    /**
     * Get the owner of the History Entry
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function historyable()
    {
        return $this->morphTo();
    }

}

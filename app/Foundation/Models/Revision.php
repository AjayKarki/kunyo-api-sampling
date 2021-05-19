<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel;

/**
 * Class Revision
 * @package Foundation\Models
 */
final class Revision extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'revisionable_type', 'revisionable_id', 'old', 'new', 'user_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'old' => 'array',
        'new' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function revisionable()
    {
        return $this->morphTo();
    }

}

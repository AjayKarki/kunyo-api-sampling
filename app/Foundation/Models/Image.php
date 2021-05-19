<?php


namespace Foundation\Models;


use Neputer\Supports\BaseModel as Model;

class Image  extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'info', 'imageable_id', 'imageable_type'
    ];

    /**
     * Get the owning Imageable model
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}

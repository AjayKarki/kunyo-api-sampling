<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'post'=>'Foundation\Models\Post'
]);
/**
 * Class Tag
 * @package Foundation\Models
 */
class Tag extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_name', 'slug', 'description', 'status',
    ];

    public function posts()
    {
        return  $this->morphedByMany(Post::class,'taggable');
    }

}

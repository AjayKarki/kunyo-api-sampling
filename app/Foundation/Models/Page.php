<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Neputer\Supports\Mixins\CrudTrait;

/**
 * Class Permission
 * @package Foundation\Models
 */
class Page extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'open_in', 'page_type', 'link', 'content', 'page_image', 'status', 'hint',
        'seo_title', 'seo_desc', 'seo_keyboards',
    ];

}

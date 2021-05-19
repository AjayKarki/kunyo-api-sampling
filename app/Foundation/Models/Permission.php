<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;
use Neputer\Supports\Access\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Permission
 * @package Foundation\Models
 */
class Permission extends Model
{

//    use RefreshesPermissionCache;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description', 'status'
    ];

    /**
     * Permission belongs to many roles
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

}

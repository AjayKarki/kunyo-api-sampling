<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Role
 * @package Foundation\Models
 */
class Role extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'description', 'status', 'parent_id',
    ];

    /**
     * Roles belongs to many permissions
     *
     * @return BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    /**
     * Roles belongs to many users
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function inRole(string $permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('slug', $permission);
        }
        return !! $permission->intersect($this->permissions)->count();
    }

    /**
     * Check whether the role contains the provided permission or not
     *
     * @param int $permission
     * @return boolean
     *
     */
    public function containsPermission($permission)
    {
        return $this->permissions->contains($permission);
    }

}

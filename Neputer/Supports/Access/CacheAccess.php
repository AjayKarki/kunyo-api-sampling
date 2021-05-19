<?php

namespace Neputer\Supports\Access;

use Foundation\Models\Permission;

final class CacheAccess
{

    const CACHE_PERMISSION_KEY = 'access-neputer';

    public static function remember()
    {
        return \Cache::remember(CacheAccess::CACHE_PERMISSION_KEY, \DateInterval::createFromDateString('24 hours'), function () {
            return Permission::with('roles')->get();
        });
    }

    public static function refreshes(): bool
    {
        \Cache::forget(CacheAccess::CACHE_PERMISSION_KEY);
        CacheAccess::remember();
    }

}

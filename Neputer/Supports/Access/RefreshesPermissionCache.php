<?php

namespace Neputer\Supports\Access;

trait RefreshesPermissionCache
{

    public static function bootRefreshesPermissionCache()
    {
        static::saved(function () {
            CacheAccess::refreshes();
        });

        static::deleted(function () {
            CacheAccess::refreshes();
        });
    }

}

<?php

namespace Foundation\Lib;

use Foundation\Models\User;

/**
 * Class Cache
 * @package Foundation\Lib
 */
final class Cache
{

    /**
     * Array of cacheable models
     */
    const CACHEABLE_MODELS = [
        User::class,
    ];

    const CACHE_ENABLED = true;

    const TIME_INTERVAL = '86400'; // 22 * 60

    public static function clear()
    {
        return \Cache::clear();
    }

    public static function forget($key)
    {
        return \Cache::forget($key);
    }

}

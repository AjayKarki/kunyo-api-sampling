<?php

namespace Neputer\Supports\Cache;

use Exception;
use Foundation\Lib\Cache;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait Cacheable
 * @package Neputer\Supports\Cache
 */
trait Cacheable
{

    /**
     * Return Cache Key
     *
     * @throws Exception
     * Throws error when cache key isn't defined
     *
     * @return mixed
     */
    public function getCacheKey() {

        if (isset($this->cacheKey)) {
            return $this->cacheKey;
        }

        throw new Exception("Cache key is not defined on ".self::class);
    }

    /**
     * Determines if individual cache is enabled.
     *
     * @return  boolean  TRUE if individual cache enabled, FALSE otherwise.
     */
    public function isIndividualCacheEnabled() {
        return $this->individualCache ?? false;
    }

    /**
     * Determines if cache all enabled.
     *
     * @return  boolean  TRUE if cache all enabled, FALSE otherwise.
     */
    public function isCacheAllEnabled() {
        return $this->cacheAll ?? Cache::CACHE_ENABLED;
    }

    /**
     * Fetches records and updates cache if no cache records
     * found initially. You can override the function with custom
     * query
     *
     * @return Model
     */
    public function fetchRecords() {
        return self::all();
    }

}

<?php

namespace Neputer\Supports;

/**
 * Class BaseConstant
 * @package Neputer\Supports
 */
abstract class BaseConstant
{

    /**
     * @return array
     */
    public static function all()
    {
        return static::$current;
    }

    /**
     * @param $index
     * @param bool $returnIndex
     * @param null $key
     * @return false|int|mixed|string|null
     */
    public static function only($index, $returnIndex = false, $key = null)
    {
        if ($returnIndex) {
            return array_search($index, static::$$key ?? static::$current, true);
        }
        return (static::$$key ?? static::$current)[$index] ?? null;
    }

    public static function get($index, $returnIndex)
    {
        return str_replace([ '-', '_', ], '', static::only($index, $returnIndex));
    }

}

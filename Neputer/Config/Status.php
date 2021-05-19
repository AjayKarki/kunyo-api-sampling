<?php

namespace Neputer\Config;

use Illuminate\Support\Arr;

/**
 * Class Status
 *
 * @package Neputer\Config
 */
final class Status
{

    private static $_instance = null;

    const DEFAULT_PAGINATION = 25;

    /**
     * Active Status
     */
    const ACTIVE_STATUS = 1;

    /**
     * InActive Status
     */
    const INACTIVE_STATUS = 0;

    /**
     * @var $current
     */
    public static $current = [
        self::ACTIVE_STATUS   => 'Active',
        self::INACTIVE_STATUS => 'In-Active',
    ];

    /**
     * Status constructor.
     */
    private function __construct () { }

    /**
     * Return the instance
     *
     * @return Status|null
     */
    public static function init ()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Add an element at the first index
     *
     * @param array $list
     * @return $this
     */
    public function prepend( array $list )
    {
        self::$current = self::merge($list , self::get());
        return $this;
    }

    /**
     * Add an element at the last index
     *
     * @param array $list
     * @return $this
     */
    public function append( array $list )
    {
        self::$current = self::merge(self::get() , $list);
        return $this;
    }

    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param array $keys
     * @return $this
     */
    public function except( array $keys )
    {
        self::$current = Arr::except(self::get() , $keys);
        return $this;
    }


    /**
     * Return all the status
     *
     * @return array
     */
    private static function get()
    {
        return self::$current;
    }

    /**
     * @return array
     */
    public static function all()
    {
        return static::get();
    }

    /**
     * Return merged array
     *
     * @param mixed ...$args
     * @return array|mixed
     */
    private static function merge(...$args)
    {
        $mergedArgs = [];
        foreach ( $args as $arg ) {
            $mergedArgs += $arg;
        }
        return $mergedArgs;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return (new static)->{$method}($args);
    }

}

<?php

namespace Modules\DevSupport\Lib;

use ArrayAccess;
use Illuminate\Support\Arr;

/**
 * Class DataExtraction
 * @package Modules\DevSupport\Lib
 */
final class DataExtraction
{

    /**
     * @param string|null $key
     * @return array|ArrayAccess|mixed
     */
    private static function extractComposer(string $key = null)
    {
        $json = file_get_contents(base_path('composer.json'));
        return $key ? Arr::get(json_decode($json, true), $key) : json_decode($json, true);
    }

    /**
     * @param string|null $key
     * @return array|ArrayAccess|mixed
     */
    public static function extractRequiredPackage(string $key = null)
    {
        $info = static::extractComposer('require');
        return $key ? Arr::get($info, $key) : $info;
    }

    /**
     * @param string|null $key
     * @return array|ArrayAccess|mixed
     */
    public static function extractServerInfo(string $key = null)
    {
        $info = [
            'version' => phpversion(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'],
            'server_os' => php_uname(),
            'database_connection_name' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'ssl_installed'  => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'),
        ];
        return $key ? Arr::get($info, $key) : $info;
    }

    /**
     * @param string|null $key
     * @return array|ArrayAccess|mixed
     */
    public static function extractAppInfo(string $key = null)
    {
        $info = [
            'version' => app()->version(),
            'timezone' => config('app.timezone'),
            'debug_mode' => config('app.debug'),
            'storage_dir_writable' => is_writable(base_path('storage')),
            'cache_dir_writable' => is_writable(base_path('bootstrap/cache')),
            'neputer_version' => neputer_version(),
        ];
        return $key ? Arr::get($info, $key) : $info;
    }

}

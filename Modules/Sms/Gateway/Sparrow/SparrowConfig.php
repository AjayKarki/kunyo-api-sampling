<?php

namespace Modules\Sms\Gateway\Sparrow;

use Neputer\Supports\ConfigInstance;

/**
 * Class SparrowConfig
 * @package Modules\Sms\Gateway\Sparrow
 */
final class SparrowConfig extends ConfigInstance
{

    const CONFIG_KEY = 'sms';

    public static function getStatus()
    {
        return static::pull(self::CONFIG_KEY, 'sparrowsms.is_enabled') ?? true;
    }

    public static function getEndpoint()
    {
        return static::pull(self::CONFIG_KEY, 'sparrowsms.endpoint');
    }

    public static function getToken()
    {
        return static::pull(self::CONFIG_KEY, 'sparrowsms.token');
    }

    public static function getIdentity()
    {
        return static::pull(self::CONFIG_KEY, 'sparrowsms.identity'); // from
    }

    public static function dbKey(): string
    {
        return 'sparrowsms_last_response';
    }

}

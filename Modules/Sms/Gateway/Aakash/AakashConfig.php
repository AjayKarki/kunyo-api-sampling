<?php

namespace Modules\Sms\Gateway\Aakash;

use Neputer\Supports\ConfigInstance;

/**
 * Class SparrowConfig
 * @package Modules\Sms\Gateway\Aakash
 */
final class AakashConfig extends ConfigInstance
{

    const CONFIG_KEY = 'sms';

    public static function dbKey(): string
    {
        return 'aakashsms_last_response';
    }

    public static function getStatus()
    {
        return static::pull(self::CONFIG_KEY, 'aakash.is_enabled') ?? true;
    }

    public static function getEndpoint()
    {
        return static::pull(self::CONFIG_KEY, 'aakash.endpoint');
    }

    public static function getToken()
    {
        return static::pull(self::CONFIG_KEY, 'aakash.token');
    }

}

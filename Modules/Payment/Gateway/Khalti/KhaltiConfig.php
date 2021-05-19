<?php

namespace Modules\Payment\Gateway\Khalti;

use Neputer\Supports\ConfigInstance;

/**
 * Class KhaltiConfig
 * @package Modules\Payment\Gateway\Khalti
 */
final class KhaltiConfig extends ConfigInstance
{

    const CONFIG_KEY = 'gateway';

    public static function getEndpoint()
    {
        return static::pull(self::CONFIG_KEY,'khalti.endpoint');
    }

    public static function isEnabled()
    {
        return static::pull(self::CONFIG_KEY, 'khalti.is_enabled');
    }

    public static function getPublicKey()
    {
        return static::pull(self::CONFIG_KEY,'khalti.publicKey');
    }

    public static function getSecretKey()
    {
        return static::pull(self::CONFIG_KEY,'khalti.secretKey');
    }

}

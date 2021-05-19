<?php

namespace Modules\Payment\Gateway\ImePay;

use Neputer\Supports\ConfigInstance;

/**
 * Class ImePayConfig
 * @package Modules\Payment\Gateway\ImePay
 */
final class ImePayConfig extends ConfigInstance
{

    const CONFIG_KEY = 'gateway';

    public static function getEndpoint()
    {
        return static::pull(self::CONFIG_KEY, 'imepay.endpoint');
    }

    public static function getMerchantCode()
    {
        return static::pull(self::CONFIG_KEY, 'imepay.merchant_code');
    }

    public static function getApiUser()
    {
        return static::pull(self::CONFIG_KEY, 'imepay.apiuser');
    }

    public static function getPassword()
    {
        return static::pull(self::CONFIG_KEY, 'imepay.password');
    }

    public static function getModule()
    {
        return static::pull(self::CONFIG_KEY, 'imepay.module');
    }

    public static function isEnabled()
    {
        return static::pull(self::CONFIG_KEY, 'imepay.is_enabled');
    }

}

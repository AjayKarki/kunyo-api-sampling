<?php

namespace Modules\Payment\Gateway\PrabhuPay;

use Neputer\Supports\ConfigInstance;

/**
 * Class PrabhuPayConfig
 * @package Modules\Payment\Gateway\PrabhuPay
 */
final class PrabhuPayConfig extends ConfigInstance
{

    const CONFIG_KEY = 'gateway';

    public static function getEndpoint()
    {
        return static::pull(self::CONFIG_KEY, 'prabhupay.endpoint');
    }

    public static function isEnabled()
    {
        return static::pull(self::CONFIG_KEY, 'prabhupay.is_enabled');
    }

    public static function getMerchantId()
    {
        return static::pull(self::CONFIG_KEY, 'prabhupay.merchantId');
    }

    public static function getPassword()
    {
        return static::pull(self::CONFIG_KEY, 'prabhupay.password');
    }

}

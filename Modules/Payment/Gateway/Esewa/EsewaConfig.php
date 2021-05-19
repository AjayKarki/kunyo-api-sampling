<?php

namespace Modules\Payment\Gateway\Esewa;

use Neputer\Supports\ConfigInstance;

final class EsewaConfig extends ConfigInstance
{

    const CONFIG_KEY = 'gateway';

    public static function isEnabled()
    {
        return ESewaConfig::pull(self::CONFIG_KEY, 'esewa.is_enabled');
    }

    public static function getEndpoint()
    {
        return ESewaConfig::pull(self::CONFIG_KEY, 'esewa.endpoint');
    }

    public static function getMerchant()
    {
        return ESewaConfig::pull(self::CONFIG_KEY, 'esewa.merchant_id');
    }

    public static function getSuccessUrl($url)
    {
        return ESewaConfig::pull(self::CONFIG_KEY, 'esewa.success_url') ?? $url;
    }

    public static function getFailureUrl($url)
    {
        return ESewaConfig::pull(self::CONFIG_KEY, 'esewa.failure_url') ?? $url;
    }

    public static function getSslVerifier()
    {
        return ESewaConfig::pull(self::CONFIG_KEY, 'esewa.ssl_verifier');
    }

}

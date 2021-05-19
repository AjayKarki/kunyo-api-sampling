<?php

namespace Modules\Payment\Gateway\FonePay;

use ArrayAccess;
use Illuminate\Config\Repository;
use Neputer\Supports\ConfigInstance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Foundation\Application;

final class FonePayConfig extends ConfigInstance
{

    const CONFIG_KEY = 'gateway';

    /**
     * Collection of CRNs
     *
     * @return string[]
     */
    public static function CRNs(): array
    {
        return [
            'NPR' => 'NPR',
        ];
    }

    public static function isEnabled()
    {
        return FonePayConfig::pull(self::CONFIG_KEY, 'fonepay.is_enabled');
    }

    /**
     * @return array|ArrayAccess|Repository|Application|Builder|mixed
     */
    public static function getEndpoint()
    {
        return FonePayConfig::pull(self::CONFIG_KEY, 'fonepay.endpoint');
    }

    /**
     * Merchant Code,Defined by fonepay system
     *
     * @return array|ArrayAccess|Repository|Application|Builder|mixed
     */
    public static function getPID()
    {
        return FonePayConfig::pull(self::CONFIG_KEY, 'fonepay.pid');
    }

    /**
     * Default Value ,NPR need to send for local merchants
     *
     * @return array|ArrayAccess|Repository|Application|Builder|mixed
     */
    public static function getCRN()
    {
        return FonePayConfig::pull(self::CONFIG_KEY, 'fonepay.crn');
    }

    /**
     * P –payment
     *
     * @return array|ArrayAccess|Repository|Application|Builder|mixed
     */
    public static function getMD()
    {
        return FonePayConfig::pull(self::CONFIG_KEY, 'fonepay.md');
    }

}

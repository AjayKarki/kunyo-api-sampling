<?php


namespace App\Foundation\Lib;

/**
 * Class DiscountVoucher
 * @package App\Foundation\Lib
 */
class DiscountVoucher
{
    const TYPE_AMOUNT = 1;
    const TYPE_PERCENT = 2;

    const USE_TYPE_GLOBAL = 1;
    const USE_TYPE_SINGLE_USER = 2;

    public static $type = [
        self::TYPE_AMOUNT => 'Amount Based',
        self::TYPE_PERCENT => 'Percent Based'
    ];

    public static $use = [
        self::USE_TYPE_GLOBAL => 'For all Customers',
        self::USE_TYPE_SINGLE_USER => 'For Individual Customer',
    ];
}

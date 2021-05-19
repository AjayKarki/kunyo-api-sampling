<?php


namespace App\Foundation\Lib;


class PaymentCountry
{
    const COUNTRY_NEPAL = 1;
    const COUNTRY_INTERNATIONAL = 2;

    public static $country = [
        self::COUNTRY_NEPAL => [
            'name' => 'Nepal',
            'currency' => 'NRS',
            'symbol' => 'Rs.',
            'enabled' => true,
        ],

        self::COUNTRY_INTERNATIONAL => [
            'name' => 'International',
            'currency' => 'USD',
            'symbol' => '$',
            'enabled' => true,
        ]
    ];

    public static $list = [
        self::COUNTRY_INTERNATIONAL => 'International | USD',
        self::COUNTRY_NEPAL => 'Nepal | NRS',
    ];

}

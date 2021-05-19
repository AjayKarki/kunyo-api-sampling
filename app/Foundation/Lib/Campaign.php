<?php


namespace App\Foundation\Lib;


final class Campaign
{
    const TYPE_EMAIL = 1;
    const TYPE_SMS = 2;

    public static $type = [
        self::TYPE_EMAIL => 'Email Campaign',
        self::TYPE_SMS => 'Sms Campaign'
    ];
}

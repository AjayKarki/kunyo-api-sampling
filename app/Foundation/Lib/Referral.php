<?php


namespace App\Foundation\Lib;


class Referral
{
    const STATUS_UNVERIFIED = 0;
    const STATUS_VERIFIED = 1;

    public static $status = [
        self::STATUS_VERIFIED => 'Verified',
        self::STATUS_UNVERIFIED => 'Un Verified'
    ];

}

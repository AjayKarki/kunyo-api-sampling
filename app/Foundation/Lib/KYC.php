<?php


namespace Foundation\Lib;


final class KYC
{
    const STATUS_UNKNOWN = 0;
    const STATUS_SUBMITTED = 1;
    const STATUS_VERIFIED = 2;
    const STATUS_REJECTED = 3;

    public static $status = [
        self::STATUS_UNKNOWN => 'Unknown/Not Submitted',
        self::STATUS_SUBMITTED => 'Application Submitted',
        self::STATUS_VERIFIED => 'Verified',
        self::STATUS_REJECTED => 'Rejected'
    ];
}

<?php


namespace App\Foundation\Lib;

/**
 * Class EmailCampaign
 * @package App\Foundation\Lib
 */
final class EmailCampaign
{
    const STATUS_NONE = 0;
    const STATUS_SENT = 1;
    const STATUS_DELIVERED = 2;
    const STATUS_OPENED = 3;

    public static $status = [
        self::STATUS_NONE => 'None',
        self::STATUS_SENT => 'Sent',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_OPENED => 'Opened'
    ];


}

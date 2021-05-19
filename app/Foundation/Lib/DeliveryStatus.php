<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class Category
 * @package App\Foundation\Lib
 */
final class DeliveryStatus extends BaseConstant
{
    const TYPE_DELIVERY_STATUS_PENDING = 0;
    const TYPE_DELIVERY_STATUS_PROCESSING = 1;
    const TYPE_DELIVERY_STATUS_COMPLETE = 2;
    const TYPE_DELIVERY_STATUS_CANCELED = 3;

    public static $types = [
        self::TYPE_DELIVERY_STATUS_PENDING => 'Pending',
        self::TYPE_DELIVERY_STATUS_PROCESSING => 'Processing',
        self::TYPE_DELIVERY_STATUS_COMPLETE => 'Complete',
        self::TYPE_DELIVERY_STATUS_CANCELED => 'Canceled',
    ];

    public static function getTypes()
    {
        return static::$types;
    }
}

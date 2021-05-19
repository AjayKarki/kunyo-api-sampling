<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class Order
 * @package Foundation\Lib
 */
final class Order extends BaseConstant
{

    const ORDER_PENDING_STATUS = 0;
    const ORDER_PROCESSING_STATUS = 1;
    const ORDER_COMPLETED_STATUS = 2;
    const ORDER_CANCELLED_STATUS = 3;

    /**
     * @var $current
     */
    public static $current = [
        self::ORDER_PENDING_STATUS    => 'PENDING',
        self::ORDER_PROCESSING_STATUS => 'PROCESSING',
        self::ORDER_COMPLETED_STATUS  => 'COMPLETED',
        self::ORDER_CANCELLED_STATUS  => 'CANCELLED',
    ];

    /**
     * @var $current
     */
    public static $transactionStatus = [
        'all'  => 'All',
        'pending' => 'Pending',
        'completed' => 'Completed',
    ];

    public static function getStatus()
    {
        return static::$current;
    }

}

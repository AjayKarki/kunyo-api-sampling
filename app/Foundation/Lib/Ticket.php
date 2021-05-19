<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class Ticket
 * @package Foundation\Lib
 */
final class Ticket extends BaseConstant
{
    const CATEGORY_ORDER = 0;
    const CATEGORY_WEBSITE = 1;
    const CATEGORY_OTHER = 2;

    const STATUS_OPEN  = 0;
    const STATUS_ASSIGNED = 1;
    const STATUS_SUPPORTED = 2;
    const STATUS_RESOLVED = 3;
    const STATUS_CLOSED  = 4;

    const PRIORITY_LOW = 0;
    const PRIORITY_MEDIUM = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_URGENT = 3;

    /**
     * @var string[] 
     */
    public static $category = [
        self::CATEGORY_ORDER => 'Order',
        self::CATEGORY_WEBSITE => 'Website',
        self::CATEGORY_OTHER => 'Other',
    ];

    /**
     * @var
     */
    public static $status = [
        self::STATUS_OPEN    => 'Opened',
        self::STATUS_ASSIGNED   => 'Assigned',
        self::STATUS_SUPPORTED   => 'Supported',
        self::STATUS_RESOLVED    => 'Resolved',
        self::STATUS_CLOSED    => 'Closed',
    ];

    /**
     * @var string[]
     */
    public static $priority = [
        self::PRIORITY_LOW => 'Low',
        self::PRIORITY_MEDIUM => 'Medium',
        self::PRIORITY_HIGH => 'High',
        self::PRIORITY_URGENT => 'Urgent',
    ];

}

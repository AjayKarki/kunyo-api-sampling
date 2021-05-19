<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class Ticket
 * @package Foundation\Lib
 */
final class TicketEvent extends BaseConstant
{
    const TYPE_COMMENT = 0;
    const TYPE_ASSIGN = 1;
    const TYPE_RESOLVED = 2;
    const TYPE_CLOSE = 3;
    const TYPE_REVOKE = 4;

    /**
     * @var string[]
     */
    public static $type = [
        self::TYPE_COMMENT => 'commented',
        self::TYPE_ASSIGN => 'assigned',
        self::TYPE_RESOLVED => 'resolved',
        self::TYPE_CLOSE => 'closed',
        self::TYPE_REVOKE => 'revoked',
    ];

}

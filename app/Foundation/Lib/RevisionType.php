<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class RevisionType
 * @package Foundation\Lib
 */
final class RevisionType extends BaseConstant
{

    const TYPE_CREATED  = 0;
    const TYPE_UPDATED  = 1;
    const TYPE_ASSIGNED = 2;
    const TYPE_DELETED  = 3;

    /**
     * @var $current
     */
    public static $current = [
        self::TYPE_CREATED    => 'Created',
        self::TYPE_UPDATED    => 'Updated',
        self::TYPE_ASSIGNED   => 'Assigned',
        self::TYPE_DELETED    => 'Deleted',
    ];

}

<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class UserMeta
 * @package Foundation\Lib
 */
final class UserMeta extends BaseConstant
{

    /**
     * Only for Other backend user except admin/super-admin
     */
    const SHOW_OWN_PICKERS = 0;
    const SHOW_ALL_PICKERS = 1;

    /**
     * @usage spanel/order/summary
     * @var $pickers
     */
    public static $pickers = [
        self::SHOW_OWN_PICKERS  => 'See only of himself',
        self::SHOW_ALL_PICKERS  => 'See of all the other pickers',
    ];

}

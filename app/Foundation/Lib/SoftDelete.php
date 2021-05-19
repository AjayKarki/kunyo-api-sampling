<?php

namespace Foundation\Lib;

/**
 * Class SoftDelete
 * @package Foundation\Lib
 */
final class SoftDelete
{

    const WITH_TRASHED = 0;
    const WITHOUT_TRASHED = 1;
    const ONLY_TRASHED = 2;

    public static $current = [
        self::WITHOUT_TRASHED => 'Without Trashed',
        self::WITH_TRASHED => 'With Trashed',
        self::ONLY_TRASHED => 'Only Trashed',
    ];

}

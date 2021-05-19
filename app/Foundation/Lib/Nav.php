<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class Nav
 * @package Foundation\Lib
 */
final class Nav extends BaseConstant
{

    const PRIMARY_MENU = 0;
    const FOOTER_PRIMARY_MENU = 1;

    public static $sections = [
        self::PRIMARY_MENU => 'Primary Menu',
        self::FOOTER_PRIMARY_MENU => 'Footer Primary Menu'
    ];

    const TYPE_CUSTOM_LINK = 0;
    const TYPE_PAGE = 1;
    const TYPE_CATEGORY = 2;
    const TYPE_POST = 3;
    const TYPE_COLLECTION = 4;

    public static $types = [
        self::TYPE_CUSTOM_LINK => 'Custom Link',
        self::TYPE_PAGE => 'Page',
        self::TYPE_CATEGORY => 'Category',
        self::TYPE_POST => 'Post',
        self::TYPE_COLLECTION => 'Collection',
    ];

    const TARGET_SELF = 0;
    const TARGET_BLANK = 1;
    const TARGET_TOP = 2;

    public static $targets = [
        self::TARGET_SELF => '_self',
        self::TARGET_BLANK => '_blank',
        self::TARGET_TOP => '_top',
    ];

    public static function getTargets()
    {
        return static::$targets;
    }

    public static function getTypes()
    {
        return static::$types;
    }

    public static function getSections()
    {
        return static::$sections;
    }

}

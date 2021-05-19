<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class HomePage
 * @package Foundation\Lib
 */
final class HomePage extends BaseConstant
{

    const HOMEPAGE_WIDGET_BEST_SELLING = 0;
    const HOMEPAGE_WIDGET_CATEGORIES = 1;
    const HOMEPAGE_WIDGET_BY_CATEGORY = 2;

    const VIEW_HOMEPAGE = 0;
    const VIEW_SINGLE_PAGE = 1;

    public static $views = [
        self::VIEW_HOMEPAGE     => 'Home / Index Page',
        self::VIEW_SINGLE_PAGE  => 'Single / Detail Page',
    ];

    /**
     * @var $current
     */
    public static $widgets = [
        self::HOMEPAGE_WIDGET_BEST_SELLING    => 'Best Selling',
        self::HOMEPAGE_WIDGET_CATEGORIES      => 'All Categories',
        self::HOMEPAGE_WIDGET_BY_CATEGORY     => 'By Category',
    ];

    /**
     * @return string[]
     */
    public static function getWidgets()
    {
        return static::$widgets;
    }

    /**
     * @return string[]
     */
    public static function getViews()
    {
        return static::$views;
    }

}

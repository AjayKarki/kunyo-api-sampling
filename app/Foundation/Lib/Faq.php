<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class Faq
 * @package App\Foundation\Lib
 */
final class Faq extends BaseConstant
{
    const TYPE_GENERAL_FAQ = 0;
    const TYPE_GIFT_CARD_FAQ = 1;
    const TYPE_IN_GAME_TOP_UP_FAQ = 2;
    const TYPE_PRODUCT_FAQ = 3;

    public static $types = [
        self::TYPE_GENERAL_FAQ => 'General',
        self::TYPE_GIFT_CARD_FAQ => 'Gift Cards',
        self::TYPE_IN_GAME_TOP_UP_FAQ => 'InGame TopUp',
        self::TYPE_PRODUCT_FAQ => 'Product',
    ];

    public static function getTypes()
    {
        return static::$types;
    }
}

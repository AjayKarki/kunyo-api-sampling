<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class Category
 * @package App\Foundation\Lib
 */
final class Category extends BaseConstant
{
    const TYPE_GENERAL_CATEGORY = 0;
    const TYPE_GIFT_CARD_CATEGORY = 1;
    const TYPE_IN_GAME_TOP_UP_CATEGORY = 2;
    const TYPE_PRODUCT_CATEGORY = 3;
    const TYPE_EXPENSE_CATEGORY = 4;

    public static $types = [
        self::TYPE_GENERAL_CATEGORY => 'General Category',
        self::TYPE_GIFT_CARD_CATEGORY => 'Gift Cards Category',
        self::TYPE_IN_GAME_TOP_UP_CATEGORY => 'InGame TopUp Category',
        self::TYPE_PRODUCT_CATEGORY => 'Product Category',
        self::TYPE_EXPENSE_CATEGORY => 'Expense Category',
    ];

    public static function getTypes()
    {
        return static::$types;
    }
}

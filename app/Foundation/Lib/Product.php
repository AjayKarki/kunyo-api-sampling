<?php

namespace Foundation\Lib;

use Neputer\Supports\BaseConstant;

/**
 * Class Product
 * @package Foundation\Lib
 */
final class Product extends BaseConstant
{

    const PRODUCT_TOP_UP_INDEX = 0;
    const PRODUCT_GIFT_CARD_INDEX = 1;

    const PRODUCT_TOP_UP = 'top-up';
    const PRODUCT_GIFT_CARD = 'gift-card';
    const PRODUCT_TOP_UP_AMOUNT = 'top-up-amount';

    const PRODUCT_PATTERN_BEST_SELLING_INDEX = 0;
    const PRODUCT_PATTERN_TOP_WISHLIST_INDEX = 1;
    const PRODUCT_PATTERN_NEW_RELEASE_INDEX  = 2;

    const PRODUCT_PATTERN_BEST_SELLING = 'Best Selling';
    const PRODUCT_PATTERN_TOP_WISHLIST = 'Top Wishlist';
    const PRODUCT_PATTERN_NEW_RELEASE  = 'Latest / New Release';

    public static array $types = [
        self::PRODUCT_TOP_UP_INDEX => 'Top Up',
        self::PRODUCT_GIFT_CARD_INDEX => 'Gift Card',
    ];

    public static array $patterns = [
        self::PRODUCT_PATTERN_BEST_SELLING_INDEX => self::PRODUCT_PATTERN_BEST_SELLING,
        self::PRODUCT_PATTERN_TOP_WISHLIST_INDEX => self::PRODUCT_PATTERN_TOP_WISHLIST,
        self::PRODUCT_PATTERN_NEW_RELEASE_INDEX  => self::PRODUCT_PATTERN_NEW_RELEASE,
    ];

    public static function getPatterns(): array
    {
        return Product::$patterns;
    }

    public static function getTypes(): array
    {
        return Product::$types;
    }

}

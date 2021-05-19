<?php

namespace Neputer\Supports\Cart\Libs;

/**
 * Class CartType
 * @package Neputer\Supports\Cart\Libs
 */
final class CartType
{

    const TYPE_DEFAULT = 0;
    const TYPE_WISHLIST = 1;

    public static function types(): array
    {
        return [
            'Cart',
            'Wish List',
        ];
    }

}

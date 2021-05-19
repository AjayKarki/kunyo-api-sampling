<?php

namespace Neputer\Supports\Cart;

use Neputer\Supports\Utility;
use Illuminate\Support\Collection;

/**
 * Class CartCollection
 * @package Neputer\Supports\Cart
 */
class CartCollection extends Collection
{

    /**
     * @return bool
     */
    public function isMultiArr()
    {
        return Utility::isMultiArr($this);
    }

}

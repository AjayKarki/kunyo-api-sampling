<?php

namespace Modules\Application\Http\DTOs\Order;

use Foundation\Models\Cart;
use Spatie\DataTransferObject\DataTransferObjectCollection;

/**
 * Class CartCollection
 * @package Modules\Application\Http\DTOs\Order
 */
final class CartCollection extends DataTransferObjectCollection
{

    public function current(): CartData
    {
        return parent::current();
    }

    public static function fromArray(array $data): CartCollection
    {
        return new CartCollection(
            array_map(fn (Cart $item) => CartData::fromModel($item), $data)
        );
    }

}

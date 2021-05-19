<?php

namespace Modules\Application\Http\DTOs\Order;

use Foundation\Models\Order;
use Modules\Payment\Payment;
use Spatie\DataTransferObject\DataTransferObjectCollection;

/**
 * Class OrderCollection
 * @package Modules\Application\Http\DTOs\Order
 */
final class OrderCollection extends DataTransferObjectCollection
{

    public function current(): OrderData
    {
        return parent::current();
    }

    public static function fromArray(array $data): OrderCollection
    {
        return new OrderCollection(
            array_map(fn (Payment $item) => OrderData::fromModel($item), $data)
        );
    }

}

<?php

namespace Modules\Application\Http\DTOs\Order;

use Foundation\Lib\Product;
use Foundation\Models\Cart;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class CartData
 * @package Modules\Application\Http\DTOs\Order
 */
final class CartData extends DataTransferObject
{

    public ?int $id;

    public ?string $type;

    public int $product_id;

    public string $identifier;

    public ?string $created_at;

    public int $quantity;

    public float $price;

    public ?array $metas;

    public static function fromModel(Cart $cart): DataTransferObject
    {
        return new self([
            'type'           => $cart->product_type ? Product::PRODUCT_GIFT_CARD : Product::PRODUCT_TOP_UP,
            'id'             => $cart->id,
            'product_id'     => $cart->product_id,
            'identifier'     => $cart->identifier,
            'quantity'       => $cart->quantity,
            'price'          => $cart->price,
            'metas'          => $cart->metas,
            'created_at'     => $cart->created_at,
        ]);
    }

}

<?php

namespace Neputer\Supports\Cart;

use Foundation\Lib\Product;
use Foundation\Models\TopUpAmount;
use Foundation\Services\GiftCardService;
use Illuminate\Support\Arr;
use Neputer\Config\Status;
use Neputer\Supports\Cart\Libs\CartType;

/**
 * Class SyncToDB
 * @package Neputer\Supports\Cart
 */
final class SyncToDB
{

    public static function add($cart, $type = CartType::TYPE_DEFAULT)
    {
        $identifier = (string) hexdec(uniqid());

        foreach ($cart as $item) {

            $item = (object) $item;

            if ($price = app('db')
                ->table(optional($item)->entity)
                ->where('id', $item->product_id)
                ->value('price')) {

                $exist = app('db')
                    ->table('carts')
                    ->where('added_by', auth()->id())
                    ->where('cart_type', $type)
                        ->where('product_id', $item->product_id)
                        ->first();

                if ($exist) {
                    $resolvedProductInfo = SyncToDB::resolveMetas($item);

                    if ($resolvedProductInfo->product) {

                        app('db')
                            ->table('carts')
                            ->where('cart_type', $type)
                            ->where('added_by', auth()->id())
//                        ->where('metas->color', $color)
                            ->where('product_id', $item->product_id)
                            ->update([
                                'added_by'      => auth()->id(),
                                'quantity'      => (int) $item->quantity, //(int) $exist->quantity +
                                'metas'         => json_encode(array_merge(optional($item)->metas ?? [], $resolvedProductInfo->metas)),
                                'updated_at'    => now(),
                                'cart_type'     => $type,
                                'product_type'  => $item->product_type,
                            ]);

                    }
                } else {

                    if ($hasOldCart = app('db')
                        ->table('carts')
                        ->where('cart_type', $type)
                        ->where('added_by', auth()->id())
                        ->first()) {
                        $identifier = $hasOldCart->identifier;
                    }

                    $resolvedProductInfo = SyncToDB::resolveMetas($item);

                    if ($resolvedProductInfo->product) {
                        app('db')
                            ->table('carts')
                            ->insert(
                                [
                                    'added_by'      => auth()->id(),
                                    'identifier'    => $identifier,
                                    'product_id'    => $item->product_id,
                                    'price'         => optional($resolvedProductInfo->product)->price,
                                    'quantity'      => $item->quantity,
                                    'metas'         => json_encode(array_merge(optional($item)->metas ?? [], $resolvedProductInfo->metas)),
                                    'cart_type'     => $type,
                                    'product_type'  => $item->product_type,
                                    'created_at'    => now(),
                                    'updated_at'    => now(),
                                ]
                            );
                    }
                }

            }

        }
    }

    private static function resolveMetas ($item): object
    {
        $productInfo = SyncToDB::getProductInfo($item->product_id, $item->product_type);

        $productType = optional($productInfo)->getTable() === 'game_top_ups_amounts' ? Product::PRODUCT_TOP_UP : Product::PRODUCT_GIFT_CARD;

        $attrs = $productType === Product::PRODUCT_TOP_UP ? [ 'attrs' => $item->attrs ?? [] ] : [];

        return (object) [
            'metas' => $productInfo ? array_merge([
                    'product_top_ups_id' => $productInfo->game_top_ups_id ?? null,
                    'title'              => $productType === Product::PRODUCT_TOP_UP ?
                        $productInfo->top_up_name : $productInfo->name,
                    'slug'               => $productType === Product::PRODUCT_TOP_UP ?
                        $productInfo->top_up_slug : $productInfo->slug,
                    'type'               => $productType,
                    'image'              => asset('storage/images/'.$productType.'/'.($productType === Product::PRODUCT_TOP_UP ?
                            $productInfo->top_up_image : $productInfo->image)),
                    'khalti_discount'    => $productInfo->khalti_discount,
                    'imepay_discount'    => $productInfo->imepay_discount,
                    'prabhupay_discount' => $productInfo->prabhupay_discount,
                ], $attrs) : [],
            'product'  => $productInfo,
        ];
    }

    private static function getProductInfo($productId, $productType)
    {
        if ($productType == Product::PRODUCT_TOP_UP_INDEX) {
            $product = TopUpAmount::query()
                ->select(
                    'game_top_ups_amounts.*',
                    'top_up.slug as top_up_slug',
                    'top_up.name as top_up_name',
                    'top_up.image as top_up_image'
                )
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(top_up.metas, '$.discount.imepay')) as imepay_discount")
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(top_up.metas, '$.discount.khalti')) as khalti_discount")
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(top_up.metas, '$.discount.prabhupay')) as prabhupay_discount")
                ->leftJoin (
                    'game_top_ups as top_up',
                    'top_up.id',
                    '=',
                    'game_top_ups_amounts.game_top_ups_id'
                )
                ->where('game_top_ups_amounts.id', $productId)
//                ->where('top_up.status', Status::ACTIVE_STATUS)
//                ->where('top_up.is_order_disable', Status::INACTIVE_STATUS)
//                    ->whereHas('category', function ($query) {
//                        return $query->where('categories.status', Status::ACTIVE_STATUS);
//                    })
                ->first();
        } else {
            $product = app(GiftCardService::class)
                ->query()
                ->where('id', $productId)
                ->select('*')
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metas, '$.discount.imepay')) as imepay_discount")
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metas, '$.discount.khalti')) as khalti_discount")
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metas, '$.discount.prabhupay')) as prabhupay_discount")
                ->where('status', Status::ACTIVE_STATUS)
//                ->where('is_order_disable', Status::INACTIVE_STATUS)
//                ->whereHas('category', function ($query) {
//                    return $query->where('categories.status', Status::ACTIVE_STATUS);
//                })
                ->first();
        }
        return $product;
    }

    public static function update ($identifier, $productId, $data)
    {
        app('db')
            ->table('carts')
            ->where('identifier', $identifier)
            ->where('product_id', $productId)
            ->update([
                'price'         => Arr::get($data, 'price') ?? 0,
                'quantity'      => Arr::get($data, 'quantity') ?? 0,
                'metas'         => Arr::get($data, 'attributes') ?? [],
                'cart_type'     => CartType::TYPE_DEFAULT,
                'added_by'      => auth()->id(),
                'updated_at'    => now(),
            ]);
    }

    public static function remove ($cartId)
    {
        app('db')
            ->table('carts')
            ->where('id', $cartId)
            ->delete();
    }

    public static function clear ($identifier)
    {
        app('db')
            ->table('carts')
            ->where('identifier', $identifier)
            ->delete();
    }

    public static function get ($type = CartType::TYPE_DEFAULT): \Illuminate\Support\Collection
    {
        return \Foundation\Models\Cart::select(
                'carts.id',
                'carts.identifier',
                'carts.product_id',
                'carts.price',
                'carts.quantity',
                'carts.metas',
                'carts.product_type',
                'carts.cart_type'
//                'product.name as product_name'
            )
//            ->leftJoin('products as product', 'product.id', '=', 'carts.product_id')
            ->where('added_by', auth()->id())
            ->where('cart_type', $type)
            ->limit(5)
            ->get();
    }

    public static function sync($cart)
    {
        $identifier = (string) hexdec(uniqid());

        foreach (Arr::get($cart, 'cart') as $item) {

            $item = (object) $item;

            if ($price = app('db')
                ->table('products')
                ->where('id', $item->product_id)
                ->value('price')) {

                if (
                $exist = app('db')
                    ->table('carts')
                    ->where('added_by', auth()->id())
                    ->where('product_id', $item->product_id)
                    ->first()
                ) {
                    app('db')
                        ->table('carts')
                        ->where('added_by', auth()->id())
                        ->where('product_id', $item->product_id)
                        ->update([
                            'quantity'      => (int) $exist->quantity + (int) $item->quantity,
                            'metas'         => json_encode(optional($item)->attributes),
                            'updated_at'    => now(),
                        ]);
                } else {

                    if ($hasOldCart = app('db')
                        ->table('carts')
                        ->where('added_by', auth()->id())
                        ->first()) {
                        $identifier = $hasOldCart->identifier;
                    }
                    app('db')
                        ->table('carts')
                        ->insert(
                            [
                                'added_by'      => auth()->id(),
                                'identifier'    => $identifier,
                                'product_id'    => $item->product_id,
                                'price'         => $price,
                                'quantity'      => $item->quantity,
                                'metas'         => json_encode(optional($item)->attributes),
                                'cart_type'     => CartType::TYPE_DEFAULT,
                                'created_at'    => now(),
                                'updated_at'    => now(),
                            ]
                        );
                }

            }

        }
    }

}

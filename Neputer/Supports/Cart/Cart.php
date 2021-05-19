<?php

namespace Neputer\Supports\Cart;

use Foundation\Handler\ProductModifier;
use Foundation\Lib\Product;
use Illuminate\Support\Arr;

/**
 * Class Cart
 * @package Neputer\Supports\Cart
 */
final class Cart
{

    const CART_KEY = 'shopping-cart';

    public static function add($id, $name = null, $price = null, $original_price = null, $quantity = null, $attributes = array())
    {
        if (is_array($id)) {
            if (CartCollection::make($id)->isMultiArr()) {
                foreach ($id as $item) {
                    static::add(
                        $item['id'],
                        $item['name'],
                        $item['price'],
                        $item['original_price'],
                        $item['quantity'],
                        $item['attributes'] ?? []
                    );
                }
            } else {
                static::add(
                    $id['id'],
                    $id['name'],
                    $id['price'],
                    $id['original_price'],
                    $id['quantity'],
                    $id['attributes'] ?? []
                );
            }

        } else {
            app('session')->put(static::CART_KEY, static::get()->reject(function ( $value, $key) use ($id) {
                return $value->id == $id;
            }));
            $cartItems = static::get();

            $newCart = $cartItems->push([
                'id'        => $id,
                'name'      => $name,
                'price'     => $price,
                'original_price' => $original_price,
                'quantity'  => $quantity,
                'attributes'=> $attributes,
                'subtotal'  => $price * $quantity
            ]);

            app('session')->put( self::CART_KEY , $newCart ?? [] );
        }
    }

    /**
     * @param string|null $value
     * @param string $key
     * @return mixed
     */
    public static function get(string $value = null, string $key = 'id') : CartCollection
    {
        $cart = CartCollection::make(app('session')->get(self::CART_KEY, []))
            ->map(function($row) {
                $newProduct = [];

                $productType = Arr::get($row, 'attributes.type');
                $productId   = Arr::get($row, 'id');

                $product = $productType === Product::PRODUCT_GIFT_CARD ?
                    ProductModifier::getGiftCard($productId) :
                    ProductModifier::getTopUp($productId);

                if ($product) {
                    $productPrice = $product->price;
                    $productOrgPrice = $product->original_price ?? 0;

                    $discount = [
                        'imepay'    => $product->imepay_discount,
                        'khalti'    => $product->khalti_discount,
                        'prabhupay' => $product->prabhupay_discount,
                        'nicasia'   => $product->nicasia_discount,
                        'esewa'   => $product->esewa_discount,
                    ];

                    $newProduct = [
                        'price'          => $productPrice,
                        'original_price' => $productOrgPrice,
                        'subtotal'       => $productPrice * ($row['quantity'] ?? 0),
                        'attributes'     => array_merge(Arr::get($row, 'attributes'), [
                            'discount'   => $discount,
                        ])
                    ];
                }

                return (object) array_merge((array) $row, $newProduct);
            });
        return $value ? $cart->firstWhere($key, $value) : $cart;
    }

    public static function clear()
    {
        app('session')->forget(self::CART_KEY);
    }

    public static function remove(string $id)
    {
        $cartItems = static::get();
        app('session')->put(static::CART_KEY, $cartItems->reject(function ( $value, $key) use ($id) {
            return $value->id == $id;
        }));
    }

    public static function total()
    {
        return static::get()->sum('subtotal');
    }

    public static function quantity()
    {
        return static::get()->count();
    }

    public static function update($data)
    {
        $collection = [];
        $cartItems = static::get();

        $newItem = array_merge($data, [ 'subtotal'  => $data['price'] * $data['quantity']]);

        foreach ($cartItems->toArray() as $cart) {
            if  (Arr::get((array) $cart, 'id') === Arr::get($newItem, 'id')) {
                $collection[] = array_merge((array) $cart, $newItem);
            } else {
                $collection[] = (array) $cart;
            }
        }
        app('session')->put( self::CART_KEY , $collection );

    }

}

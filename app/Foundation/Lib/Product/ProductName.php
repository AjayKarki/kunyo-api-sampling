<?php

namespace Foundation\Lib\Product;

use Foundation\Lib\Product;

/**
 * Class ProductName
 * @package Foundation\Lib\Product
 */
final class ProductName
{

    // OrderType|OrderId|ProductId|ProductName|Quantity
    const PRODUCT_NAME_SEPARATOR = '|';

    // If Multiple
    // OrderType|OrderId|ProductId|ProductName|Quantity _|_ OrderType|OrderId|ProductId|ProductName|Quantity
    const PRODUCT_NAME_MULTIPLE_SEPARATOR = ' _|_ ';

    /**
     * @param $transactionId
     * @return string
     */
    public static function generate($transactionId)
    {
        $orders = app('db')->table('orders')
            ->select(
                'order_type', 'order_type_id', 'quantity'
            )
            ->where('transaction_id', $transactionId)
            ->get();

        $data = [];

        foreach ($orders as $order) {

            if ($order->order_type == \Foundation\Lib\Product::PRODUCT_TOP_UP_INDEX) {
                $product = app('db')
                    ->table('game_top_ups_amounts')
                    ->select('game_top_ups_amounts.game_top_ups_id')
                    ->selectRaw('concat_ws(" - ",top_ups.name,game_top_ups_amounts.title) as group_title')
                    ->leftJoin('game_top_ups as top_ups', 'top_ups.id', '=', 'game_top_ups_amounts.game_top_ups_id')
                    ->where('game_top_ups_amounts.id', $order->order_type_id)
                    ->first();

                if ($product) {
                    $data[] = implode(ProductName::PRODUCT_NAME_SEPARATOR, [
                        $order->order_type,
                        $order->order_type_id,
                        $product->game_top_ups_id,
                        $product->group_title,
                        $order->quantity,
                    ]);
                }
            } else {
                $product = app('db')
                    ->table('gift_cards')
                    ->select('name', 'id')
                    ->where('id', $order->order_type_id)
                    ->first();

                if ($product) {
                    $data[] = implode(ProductName::PRODUCT_NAME_SEPARATOR, [
                        $order->order_type,
                        $order->order_type_id,
                        $product->id,
                        $product->name,
                        $order->quantity,
                    ]);
                }
            }

        }

        return implode(ProductName::PRODUCT_NAME_MULTIPLE_SEPARATOR, $data);
    }

    public static function name($name)
    {
        return explode(ProductName::PRODUCT_NAME_SEPARATOR, $name);
    }

    public static function getOrderType($name)
    {
        $product = ProductName::name($name);
        return $product[0] ?? 'N/A Order Type';
    }

    public static function getOrderId($name)
    {
        $product = ProductName::name($name);
        return $product[1] ?? 'N/A Order ID';
    }

    public static function getProductId($name)
    {
        $product = ProductName::name($name);
        return $product[2] ?? 'N/A Product ID';
    }

    public static function getProductName($name)
    {
        $product = ProductName::name($name);
        return $product[3] ?? 'N/A Product Name';
    }

    public static function getQuantity($name)
    {
        $product = ProductName::name($name);
        return $product[4] ?? 'N/A Quantity';
    }

    public static function isMultiple($name): bool
    {
        $products = explode(ProductName::PRODUCT_NAME_MULTIPLE_SEPARATOR, $name);
        return count($products) > 1;
    }

    public static function getQuantities($name)
    {
        //OrderType|OrderId|ProductId|ProductName|Quantity
        if (ProductName::isMultiple($name)) {
            $products = explode(ProductName::PRODUCT_NAME_MULTIPLE_SEPARATOR, $name);

            $quantity = [
                'total' => 0,
            ];
            foreach ($products as $product) {
                $productInfo = explode(ProductName::PRODUCT_NAME_SEPARATOR, $product);
                $quantity[Product::$types[$productInfo[0] ?? 0]] =  ($quantity[Product::$types[$productInfo[0] ?? 0]] ?? 0) + $productInfo[4];
                $quantity['total'] +=  $productInfo[4];
            }

            return $quantity;
        }
        return ProductName::name($name)[4] ?? 0;
    }

    public static function resolveProductName($id, $type): ?string
    {
        if (Product::PRODUCT_TOP_UP_INDEX == $type) {
            return app('db')
                ->table('game_top_ups_amounts')
                ->selectRaw('concat_ws(" - ",top_ups.name,game_top_ups_amounts.title) as group_title')
                ->leftJoin('game_top_ups as top_ups', 'top_ups.id', '=', 'game_top_ups_amounts.game_top_ups_id')
                ->where('game_top_ups_amounts.id', $id)
                ->value('group_title');
        }

        return app('db')
            ->table('gift_cards')
            ->where('id', $id)
            ->value('name');
    }

}

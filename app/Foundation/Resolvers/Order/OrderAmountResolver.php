<?php

namespace Foundation\Resolvers\Order;

use Foundation\Lib\Order;
use Foundation\Services\OrderService;
use Modules\Payment\PaymentService;

/**
 * Class OrderAmountResolver
 * @package Foundation\Resolvers\Product
 */
final class OrderAmountResolver
{
    /**
     *  Get Amount for Orders using transaction ID
     *
     * @param array | string $transactionId
     * @param bool $onlyPending
     * @return array
     */
    public static function getFromId($transactionId, $onlyPending = false)
    {
        $transactions = app(PaymentService::class)->getUnpickedFromId($transactionId);
        return self::getAmount($transactions, $onlyPending);
    }

    /**
     *  Get Amount for Orders using transaction ID
     *
     * @param array | string $transactionId
     * @return array
     */
    public static function getFromIds($transactionId): array
    {
        $transactions = app(OrderService::class)->find($transactionId);
        return self::getAmount($transactions);
    }

    /**
     * Get Amount for Single Transaction
     *
     * @param $transaction
     * @param bool $onlyPending
     * @return array
     */
    public static function getFromTransaction($transaction, $onlyPending = false)
    {
        $amounts = self::getAmount([$transaction], $onlyPending);
        return $amounts[0];
    }

    /**
     * Get Amount for Collection of Transactions
     *
     * @param $transactions
     * @param bool $onlyPending
     * @return array
     */
    public static function getAmount($transactions, $onlyPending = false)
    {
        $amounts = [];
        foreach ($transactions as $transaction){
            if ($transaction->product_type == 1){ //@TODO: Change to Product::SYSTEM_PRODUCT after merging feature/gaming-product

                if ($onlyPending)
                    $orders = $transaction->orders->where('delivery_status', '!=', Order::ORDER_COMPLETED_STATUS);
                else
                    $orders = $transaction->orders;

                $orderAmounts = [];
                foreach ($orders as $order){
                    array_push($orderAmounts, [
                        'order_id' => $order->id,
                        'amount' => ($order->quantity * $order->amount) - $order->discounted_amount,
                        'order_type' => $order->order_type
                    ]);
                }

                if (sizeof($orderAmounts) > 0)
                    array_push($amounts, $orderAmounts);
            }
        }
        return $amounts;
    }

}

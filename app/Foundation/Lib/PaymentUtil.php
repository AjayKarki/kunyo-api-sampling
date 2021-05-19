<?php

namespace Foundation\Lib;

use Foundation\Models\KunyoCurrency;
use Modules\Payment\Libs\Payment as PaymentConstant;
use Modules\Payment\Payment;

/**
 * Class PaymentUtil
 * @package Foundation\Lib
 */
final class PaymentUtil
{

    /**
     * Return the resolved amount for the transaction for imePay payment
     *
     * @param $transaction
     * @param $orders
     * @return string
     */
    public static function resolveImePayAmount($transaction, $orders)
    {
        $orderFinalPrice = (static::getTotalPrice($orders) +
                static::getGatewayServiceCharge((optional($transaction)->service_charge ?? 0)));
        return static::getPrice($orderFinalPrice - $transaction->voucher_discount);
    }

    /**
     * Return the resolved amount for the transaction for prabhuPay payment
     *
     * @param $transaction
     * @param $orders
     * @return string
     */
    public static function resolvePrabhuPayAmount($transaction, $orders)
    {
        $orderFinalPrice = (static::getTotalPrice($orders) +
                static::getGatewayServiceCharge((optional($transaction)->service_charge ?? 0)));

        return static::getPrice($orderFinalPrice - $transaction->voucher_discount);
    }

    /**
     * Return the resolved amount for the transaction for khalti payment
     *
     * @param $transaction
     * @param $orders
     * @return string
     */
    public static function resolveKhaltiAmount($transaction, $orders)
    {
        $orderFinalPrice = ((static::getTotalPrice($orders) +
                static::getGatewayServiceCharge((optional($transaction)->service_charge ?? 0))) - $transaction->voucher_discount) * 100;
        return static::getPrice($orderFinalPrice);
    }

    /**
<<<<<<< HEAD
     * Return the resolved amount for the transaction for nicAsia payment
     *
     * @param $transaction
     * @param $orders
     * @return string
     */
    public static function resolveNicAsiaAmount($transaction, $orders): string
    {
        $orderFinalPrice = (PaymentUtil::getTotalPrice($orders) +
            PaymentUtil::getGatewayServiceCharge((optional($transaction)->service_charge ?? 0)));

        return PaymentUtil::getPrice($orderFinalPrice - $transaction->voucher_discount);
    }

    /**
     * Return the resolved amount for the transaction for esewa payment
     *
     * @param $transaction
     * @param $orders
     * @return string
     */
    public static function resolveEsewaAmount($transaction, $orders): string
    {
        $orderFinalPrice = (PaymentUtil::getTotalPrice($orders) +
            PaymentUtil::getGatewayServiceCharge((optional($transaction)->service_charge ?? 0)));

        return PaymentUtil::getPrice($orderFinalPrice - $transaction->voucher_discount);
    }

    /**
     * Get the total amount to be paid
     *
     * @param $transaction
     * @return string
     */
    public static function resolveAmount($transaction)
    {
        $class = get_class($transaction);
        if($class == KunyoCurrency::class)
            return self::getPrice($transaction->amount);

        elseif ($class == Payment::class){
            $orders = $transaction->orders;
            $gateway = PaymentConstant::gateways()[$transaction->payment_gateway_id];
            $function = 'resolve' . str_replace(' ', '', $gateway) . 'Amount';
            return self::{$function}($transaction, $orders);
        }
        return 0;
    }

    /**
     * Return total price for the transactions
     *
     * @param $orders
     * @return float|int
     */
    private static function getTotalPrice($orders)
    {
        $price = 0;

        $countOfOrders = count((array) $orders);

        if ($countOfOrders > 0) {
            foreach ($orders  as $order) {
                if ($order->discounted_amount) {
                    $price += (($order->quantity * $order->amount) - $order->discounted_amount);
                } else {
                    $price += ($order->quantity * $order->amount);
                }
            }
        }

        return $price;
    }

    /**
     * Return the service charge for any gateway
     *
     * @param $serviceCharge
     * @return string|string[]
     */
    private static function getGatewayServiceCharge($serviceCharge)
    {
        return str_replace(',', '',  $serviceCharge);
    }

    /**
     * Return the price with only 2 decimals
     *
     * @param $price
     * @return string
     */
    private static function getPrice($price)
    {
        return number_format((float) $price, 2, '.', '');
    }

}

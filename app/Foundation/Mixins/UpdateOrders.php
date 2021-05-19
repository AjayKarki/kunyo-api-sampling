<?php

namespace Foundation\Mixins;

use Foundation\Lib\Order;

/**
 * Trait UpdateOrders
 * @package Foundation\Mixins
 */
trait UpdateOrders
{

    private function updateGiftCardOrder($order)
    {
        $giftCardCode = app('db')
            ->table('gift_cards_codes')
            ->inRandomOrder()
            ->where('gift_cards_codes.is_used', 0)
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->where('gift_cards_codes.gift_cards_id', $order->order_type_id ?? 0)
            ->first();

        if ($giftCardCode) {

            \Log::info('Automated Update order items', [
                'ORDER' => $order->id,
                'Form CODES' => $giftCardCode->id,
            ]);

            $this->orderService->addGiftCardCodes($giftCardCode->id, $order->id);

            $deliveredQuantity = app('db')
                ->table('orders_gift_cards_codes')
                ->where('order_id', $order->id)
                ->count();

            if ($order->quantity === $deliveredQuantity) {
                $order->update([
                    'delivery_status' => Order::ORDER_COMPLETED_STATUS,
                ]);
            }
        }
    }

    private function updateTopUpOrder($order) {
        $this->orderService->addTopUpAmounts($order);
    }

}

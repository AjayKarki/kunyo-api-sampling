<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Lib\Order;
use Foundation\Lib\Product;
use Foundation\Services\GiftCardService;
use Illuminate\Http\Request;
use Foundation\Services\OrderService;

/**
 * Class UpdateOrderStatus
 * @package App\Http\Controllers\Admin\Actions
 */
final class UpdateOrderStatus
{

    private $orderService;
    private $giftCardService;

    public function __construct(
        OrderService $orderService,
        GiftCardService $giftCardService
    )
    {
        $this->orderService = $orderService;
        $this->giftCardService = $giftCardService;
    }

    public function __invoke(Request $request)
    {
        $response = false;
        $message = 'Cannot update the delivery status for the given order. Order quantity is not already fullfilled.';

        $order = $this->orderService->query()->find($request->get('pk'));
        if ($order) {
            if ($order->order_type === Product::PRODUCT_GIFT_CARD_INDEX) {
                if ($this->giftCardService->getGiftCardCode($order->id)->count() === $order->quantity) {
                    $order->update([
                        'delivery_status' => $request->get('value'),
                    ]);
                    $response = true;
                    $message = 'You have successfully '. Order::$current[$request->get('value')] . ' the order.';
                }
            }
        }
        return response()
            ->json([
                'success' => $response,
                'message' => $message,
            ]);
    }

}

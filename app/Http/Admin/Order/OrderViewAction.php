<?php

namespace App\Http\Controllers\Admin\Order;

use Foundation\Lib\Role;
use Foundation\Models\Order;
use Foundation\Services\UserService;
use Neputer\Supports\BaseController;
use Foundation\Services\TopUpService;
use Foundation\Services\OrderService;
use Foundation\Services\GiftCardService;

/**
 * Class OrderViewAction
 *
 * @package App\Http\Controllers\Admin\Order
 */
final class OrderViewAction extends BaseController
{

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var TopUpService
     */
    private $topUpService;

    /**
     * @var GiftCardService
     */
    private $giftCardService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * OrderViewAction constructor.
     *
     * @param OrderService $orderService
     * @param TopUpService $topUpService
     * @param GiftCardService $giftCardService
     * @param UserService $userService
     */
    public function __construct(
        OrderService $orderService,
        TopUpService $topUpService,
        GiftCardService $giftCardService,
        UserService $userService
    )
    {
        $this->orderService = $orderService;
        $this->topUpService = $topUpService;
        $this->giftCardService = $giftCardService;
        $this->userService     = $userService;
    }

    /**
     * @param Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function __invoke(Order $order)
    {
        $data['order'] = $order->load('transaction.user');

        if (isset($data['order']->transaction->picked_by) && $userId = $data['order']->transaction->picked_by) {
            $data['assigned-manager'] = app('db')
                ->table('users')
                ->selectRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(middle_name,''),' ',COALESCE(last_name,'')) AS full_name")
                ->where('id', $userId)
                ->value('full_name');
        }

        if ($order->order_type === \Foundation\Lib\Product::PRODUCT_TOP_UP_INDEX) {
            $data['product'] = $this->topUpService->getDetail($order->order_type_id);
            $data['player-info'] = $order->playerInfo->groupBy('batch');
        } else {
            $data['product'] = $this->giftCardService->getDetail($order->order_type_id);
            $data['codes'] = $this->giftCardService->getInActiveCodes($order->order_type_id);
        }

        return view('admin.order.single-order-view', compact('data'));
    }

}

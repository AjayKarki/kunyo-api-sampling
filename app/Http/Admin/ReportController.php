<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Foundation\Lib\Date;
use Foundation\Lib\Meta;
use Foundation\Lib\Order as OrderConst;
use Foundation\Lib\Product;
use Foundation\Models\Order;
use Foundation\Services\OrderService;
use Illuminate\Http\Request;
use Modules\Payment\Libs\Payment;
use Modules\Payment\PaymentService;
use Neputer\Config\Status;
use Neputer\Supports\BaseController;
use Neputer\Supports\Utility;

final class ReportController extends BaseController
{

    private $orderService;

    private $paymentService;

    public function __construct(
        OrderService $orderService,
        PaymentService $paymentService
    )
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request)
    {
        $data = [];

        $date = $request->get('date-type') ?? Date::DATE_TODAY;
        $gateway = $request->get('gateway-type') ?? 'all';
        $type = $request->get('product-type') ?? 'all';
        $dateRange = explode(' - ', $request->get('date-range'));

//        \DB::enableQueryLog();
        $data['statistics'] = app('db')
            ->table('orders')
            ->select([
                'orders.id as orderID',
                'orders.amount as amount',
                'orders.created_at',
                'orders.quantity as quantity',
                'orders.order_type_id',
                'orders.order_type',
                'game_top_ups.id as top_up_id',
                'game_top_ups.name as top_up_name',
                'game_top_ups.image as top_up_image',
                'gift_cards.id as gift_card_id',
                'gift_cards.name as gift_card_name',
                'gift_cards.image as gift_card_image',
                'gift_cards.price as gift_card_price',
                'transactions.id as transactionId',
                'transactions.transaction_id as trackingCode',
                'transactions.payment_gateway_id',
                'transactions.status',
                'transactions.user_id as orderedBy',
            ])
            ->selectRaw("
                CONCAT(
                COALESCE(DATEDIFF('".now()."', transactions.created_at),'')
                ,' Day Ago - ',
                COALESCE(DATE_FORMAT(transactions.created_at, '%M %d %Y'),'')
                ) AS invoked_date_time"
            )
//            ->selectRaw("GROUP_CONCAT(orders.transaction_id SEPARATOR ', ') as transaction_ids")
            ->selectRaw("CASE WHEN game_top_ups.name IS NOT NULL THEN game_top_ups.name ELSE gift_cards.name END AS product_name")
            ->selectRaw("SUM(CASE WHEN orders.delivery_status = '". OrderConst::ORDER_COMPLETED_STATUS."' THEN IFNULL(transactions.service_charge, 0) END) AS total_service_charge")
            ->selectRaw("SUM(CASE WHEN orders.delivery_status = '".OrderConst::ORDER_COMPLETED_STATUS."' THEN IFNULL(orders.discounted_amount, 0) END) AS total_discounted_price")

            ->selectRaw("SUM(CASE WHEN orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' THEN orders.quantity END) AS total_top_up_quantity")
            ->selectRaw("SUM(CASE WHEN orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' AND orders.delivery_status = '".OrderConst::ORDER_COMPLETED_STATUS."' THEN orders.quantity END) AS total_delivered_top_up_quantity")
            ->selectRaw("SUM(CASE WHEN orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' AND orders.delivery_status = '".OrderConst::ORDER_PENDING_STATUS."' THEN orders.quantity END) AS total_pending_top_up_quantity")
            ->selectRaw("SUM(CASE WHEN orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' AND orders.delivery_status = '".OrderConst::ORDER_COMPLETED_STATUS."' THEN (orders.quantity * orders.amount) END) AS total_top_up_amount")

            ->selectRaw("SUM(CASE WHEN orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' THEN orders.quantity END) AS total_gift_card_quantity")
            ->selectRaw("SUM(CASE WHEN orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' AND orders.delivery_status = '".OrderConst::ORDER_COMPLETED_STATUS."' THEN orders.quantity END) AS total_delivered_gift_card_quantity")
            ->selectRaw("SUM(CASE WHEN orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' AND orders.delivery_status = '".OrderConst::ORDER_PENDING_STATUS."' THEN orders.quantity END) AS total_pending_gift_card_quantity")
            ->selectRaw("SUM(CASE WHEN orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' AND orders.delivery_status = '".OrderConst::ORDER_COMPLETED_STATUS."' THEN (orders.quantity * orders.amount) END) AS total_gift_card_amount")

            ->leftJoin('transactions', 'transactions.id', '=', 'orders.transaction_id')
            ->leftJoin('game_top_ups_amounts', function($query) {
                $query->on('game_top_ups_amounts.id', '=', 'orders.order_type_id')
                    ->where('orders.order_type', Product::PRODUCT_TOP_UP_INDEX)
                    ->groupBy('orders.order_type_id');
            })
            ->leftJoin('game_top_ups', function($query) {
                $query->on('game_top_ups.id', '=', 'game_top_ups_amounts.game_top_ups_id')
                    ->where('orders.order_type', Product::PRODUCT_TOP_UP_INDEX)
                    ->groupBy('orders.order_type_id');
            })
            ->leftJoin('gift_cards', function($query) {
                $query->on('gift_cards.id', '=', 'orders.order_type_id')
                    ->where('orders.order_type', Product::PRODUCT_GIFT_CARD_INDEX)
                    ->groupBy('orders.order_type_id');
            })
            ->when(!is_null($date), function ($query) use ($date, $dateRange) {
                if ($date === "custom-date-range") {
                    $query->whereDate('transactions.created_at', '>=', $dateRange[0] ?? now());
                    $query->whereDate('transactions.created_at', '<=', $dateRange[1] ?? now());
                    return $query;
                }
                switch ($date){
                    case Date::DATE_TODAY:
                        $query->whereDate('orders.created_at', today());
                        break;
                    case Date::DATE_YESTERDAY:
                        $query->whereDate('orders.created_at', Carbon::yesterday());
                        break;
                    case Date::DATE_WEEK:
                        $query->whereBetween('orders.created_at', [ now()->startOfWeek(), now()->endOfWeek() ]);
                        break;
                    case Date::DATE_MONTH:
                        $query->whereBetween('orders.created_at', [ now()->startOfMonth(), now()->endOfMonth() ]);
                        break;
                    case Date::DATE_YEAR:
                        $query->whereBetween('orders.created_at', [ now()->startOfYear(), now()->endOfYear() ]);
                        break;
                    case "custom-date-range":
                        $query->whereDate('transactions.created_at', '>=', $dateRange[0] ?? now());
                        $query->whereDate('transactions.created_at', '<=', $dateRange[1] ?? now());
                        break;
                }
            })
            ->when(!is_null($gateway), function ($query) use ($gateway) {
                if ($gateway === 'all') {
                    $query->whereIntegerInRaw('transactions.payment_gateway_id', array_keys(Payment::gateways()));
                } else {
                    $query->where('transactions.payment_gateway_id', $gateway);
                }
            })
            ->when(!is_null($type), function ($query) use ($type) {
                if ($type === 'all') {
                    $query->whereIntegerInRaw('orders.order_type', array_keys(Product::getTypes()));
                } else {
                    $query->where('orders.order_type', $type);
                }
            })
            ->groupBy(app('db')->raw("product_name"))
            ->orderBy('orders.created_at', 'DESC')
            ->get();

        $data['payment-stats'] = $this->paymentService->sortByUse($date, 'ASC') ?? [];

        return view('admin.reports.index', compact('data'));
    }

}

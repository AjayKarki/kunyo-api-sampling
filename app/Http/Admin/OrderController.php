<?php

namespace App\Http\Controllers\Admin;

use App\Foundation\Lib\TopupPlayerInformation;
use Foundation\Services\KunyoCurrencyService;
use Foundation\Services\MV\OrderRecordService;
use Foundation\Services\SettingService;
use Foundation\Services\TopupPlayerInformationService;
use Foundation\Services\TransactionConversationService;
use Throwable;
use Exception;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Modules\Payment\Payment;
use Modules\Payment\PaymentService;
use Foundation\Lib\Statistics\Chart;
use Neputer\Supports\BaseController;
use Foundation\Services\OrderService;
use Illuminate\Contracts\View\Factory;
use Foundation\DataTables\OrderDataTable;

/**
 * Class OrderController
 * @package App\Http\Controllers\Admin
 */
final class OrderController extends BaseController
{

    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * @var OrderService
     */
    private $orderService;
    /**
     * @var SettingService
     */
    private $settingService;

    private $orderRecordService;

    /**
     * OrderController constructor.
     * @param PaymentService $paymentService
     * @param OrderService $orderService
     * @param SettingService $settingService
     * @param OrderRecordService $orderRecordService
     */
    public function __construct(
        PaymentService $paymentService,
        OrderService $orderService,
        SettingService $settingService,
        OrderRecordService $orderRecordService
    )
    {
        $this->paymentService = $paymentService;
        $this->orderService   = $orderService;
        $this->settingService = $settingService;
        $this->orderRecordService  = $orderRecordService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     * @throws Throwable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return OrderDataTable::init(
                $this->paymentService->filter($request->only('filter', 'search.value'))
//                $this->orderRecordService->filter($request->only('filter', 'search.value'))
            );
        }
        $data['order-checks'] = $this->paymentService->query()
            ->select(
                'id', 'recheck_status', 'rechecked_date', 'recheck_asked_date', 'recheck_message', 'transaction_id'
            )
            ->where('recheck_status', 1)
            ->limit(8)
            ->latest()
            ->get();
        $data['resubmit'] = app(TopupPlayerInformationService::class)->getPlayerInfo(TopupPlayerInformation::STATUS_SUBMITTED)->groupBy('batch');
        return view('admin.order.index', compact('data'));
    }

    public function datatable(Request $request)
    {
        return OrderDataTable::init(
            $this->paymentService->filter($request->only('filter', 'search.value'))
        );
    }

    public function show(Payment $payment)
    {
        $data['transaction'] = $payment
            ->load([ 'orders', 'user', 'conversations.author:id,first_name,middle_name,last_name,image' ]);
        $data['attrs']       = app(PaymentService::class)->getBankAttrs($payment->id);
        $data['unreadMessageCount'] = $payment->conversations->where('acknowledged_by_admin', false)->count();
        app(TransactionConversationService::class)->markAsReadByAdmin($payment->id);
        return view('admin.order.show', compact('data'));
    }

    public function summary(Request $request)
    {
        $data = [];

        if($request->ajax()) {
            $totalOrders = $this->orderService->getTotalOrderStats(null, $request->get('summary_from_date'), $request->get('summary_to_date'), true);
            $totalOrdersToday = $this->orderService->getTotalOrderStats(today());

            $currency = app(KunyoCurrencyService::class)->salesStats(null, null, $request->get('summary_from_date'), $request->get('summary_to_date'), true);
            $currencyToday = app(KunyoCurrencyService::class)->salesStats(null, today());

            // Total Number of Order
            $data['total-exact-orders'] = [
                'today' => nrp($totalOrdersToday->total_exact_order ?? 0, 0),
                'all' => nrp($totalOrders->total_exact_order ?? 0, 0),
            ];

            // Number of actual items in order
            $data['total-orders'] = [
                'today' => [
                    'quantity' => nrp($totalOrdersToday->total_order ?? 0, 0),
                    'amount' => nrp($totalOrdersToday->total_amount_earned ?? 0),
                ],
                'all' => [
                    'quantity' => nrp($totalOrders->total_order ?? 0, 0),
                    'amount' => nrp($totalOrders->total_amount_earned ?? 0),
                ]
            ];

            $data['total-gift-card'] = [
                'today' => [
                    'quantity' => nrp($totalOrdersToday->total_gift_card ?? 0, 0),
                    'amount' => nrp($totalOrdersToday->total_gift_card_amount ?? 0),
                ],
                'all' => [
                    'quantity' => nrp($totalOrders->total_gift_card ?? 0, 0),
                    'amount' => nrp($totalOrders->total_gift_card_amount ?? 0),
                ],
            ];

            $data['total-top-up'] = [
                'today' => [
                    'quantity' => nrp($totalOrdersToday->total_top_up ?? 0, 0),
                    'amount' => nrp($totalOrdersToday->total_top_up_amount ?? 0),
                ],
                'all' => [
                    'quantity' => nrp($totalOrders->total_top_up ?? 0, 0),
                    'amount' => nrp($totalOrders->total_top_up_amount ?? 0),
                ],
            ];

            $data['kunyo-currency'] = [
                'today' => [
                    'quantity' => nrp($currencyToday->quantity, 0),
                    'amount' => nrp($currencyToday->revenue)
                ],
                'total' => [
                    'quantity' => nrp($currency->quantity),
                    'amount' => nrp($currency->revenue)
                ],
            ];

            return response()->json($data);
        }

        $data['settings'] = $this->settingService->getSettings();
        return view('admin.order.summary', compact('data'));
    }

    /**
     * Get Data for Line charts and Pie charts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSummaryCharts(Request $request)
    {
        $data = [];

        $dailyDate = $request->get('dailyDate');
        $startDate = $request->get('startDate') ?? Carbon::now()->subDays(30);
        $endDate = $request->get('endDate') ?? Carbon::now();

        $totalOrdersForCharts = $this->orderService->getTotalOrderStats($dailyDate, $startDate, $endDate);

        $data['lastNDaysData'] = $this->getLineChartData($this->orderService->getOrderStatsForAPeriod($dailyDate, $startDate, $endDate));
        $data['lastNDaysAmount'] = $this->getLineChartData($this->orderService->getOrderAmountsForAPeriod($dailyDate, $startDate, $endDate));

        // Pending, Complete, Cancel
        $data['order-pie-chart-by-status'] = $this->getOrderStatistics(
            ['pending', $totalOrdersForCharts->pending_orders, '#1ab366'],
            ['processing', $totalOrdersForCharts->processing_orders, '#1abaeb'],
            ['completed', $totalOrdersForCharts->completed_orders, '#1ab394'],
            ['cancelled', $totalOrdersForCharts->cancelled_orders, '#BABABA']
        );

        // Gift Card, Top-up
        $data['order-pie-chart-by-type'] = $this->getOrderStatistics(
            ['topup', $totalOrdersForCharts->total_top_up, '#1ab394'],
            ['giftCard', $totalOrdersForCharts->total_gift_card, '#1a3b94'],
            [ 'Product',  0, '#BABABA']
        );

        return response()->json($data);
    }

    /**
     * Get Picked Order Statistics grouped by picker
     * @param Request $request
     * @return string
     * @throws Exception|Throwable
     */
    public function getStatsByPicker(Request $request)
    {
        if ($request->ajax()) {
            return datatables()
                ->of($this->orderService->getStatsGroupedByPicker($request->get('filter')))
                ->addColumn('user', function ($data) {
                    return $data->full_name;
                })
                ->addColumn('pick_count', function ($data) {
                    return view('admin.order.partials.pick-count', compact('data'))->render();
                })
                ->addColumn('last_order', function ($data) {
                    $data->created_at = Carbon::parse($data->created_at);
                    return view('admin.common.created-at', compact('data'))->render();
                })
                ->addColumn('action', function ($data) {
                    return '';
                })
                ->rawColumns(['pick_count', 'action', 'last_order', ])
                ->make(true);
        }
        return '';
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     * @throws Throwable
     */
    public function redeemList(Request $request)
    {
        if ($request->ajax()) {

            return OrderDataTable::redeem($this->paymentService->filterRedeem($request->only('filter', 'search.value')));
        }

        return view('admin.order.redeem-list');
    }

    private function getOrderStatistics(...$data)
    {
        $columns = [];
        $colours = [];
        foreach ($data as $item){
            array_push($columns, [ ucfirst($item[0]), $item[1] ]);
            array_push($colours, [ $item[0] => $item[2] ]);
        }
        return Chart::getPieChart($columns, $colours);
    }

    private function getLineChartData($data)
    {
        $orders = [];
        foreach ($data as $value){
            $item = [
                'period' => $value->date,
                'giftcard' => $value->total_gift_card,
                'topup' => $value->total_top_up,
                'total' => $value->total_gift_card + $value->total_top_up,
            ];
            array_push($orders, (object)$item);
        }
        return $orders;
    }

}

<?php

namespace Foundation\Services;

use App\Foundation\Lib\History;
use Carbon\Carbon;
use Foundation\Lib\Date;
use Foundation\Lib\Meta;
use Foundation\Lib\Product;
use Foundation\Models\GiftCardsCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Modules\Payment\Libs\Payment as PaymentConst;
use Modules\Payment\Payment;
use Foundation\Models\Order;
use Neputer\Supports\BaseService;
use Illuminate\Support\Facades\Auth;
use Foundation\Lib\Order as OrderLib;
use Illuminate\Database\Eloquent\Collection;
use Neputer\Supports\Utility;

/**
 * Class OrderService
 * @package Foundation\Services
 */
class OrderService extends BaseService
{

    /**
     * The Order instance
     *
     * @var $model
     */
    protected $model;

    /**
     * OrderService constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->model = $order;
    }

    /**
     * Filter
     *
     * @param string|null $name
     * @return mixed
     */
    public function filter(string $name = null)
    {
        return $this->model
            ->select(
                'orders.*',
                'transactions.transaction_id as transaction_code',
                'transactions.payment_gateway_id'
            )
            ->selectRaw('CONCAT_WS(" ", users.first_name, users.middle_name, users.last_name) AS full_name')
            ->leftJoin('transactions', 'transactions.id', '=', 'orders.transaction_id')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->where(function ($query) use ($name){
                if($name){
                    $query->where('orders.transaction_id', $name)
                        ->orWhere('orders.order_id', $name);
                }
            });
    }

    public function filterAssignedOrder(string $name = null)
    {
        return $this->model
            ->select(
                'orders.*',
                'transactions.transaction_id as transaction_code',
                'transactions.payment_gateway_id'
            )
            ->selectRaw('CONCAT_WS(" ", users.first_name, users.middle_name, users.last_name) AS full_name')
            ->leftJoin('transactions', 'transactions.id', '=', 'orders.transaction_id')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->where(function ($query) use ($name){
                if($name){
                    $query->where('orders.transaction_id', $name)
                        ->orWhere('orders.order_id', $name);
                }
            })
            ->where('orders.assigned_to', auth()->id())
            ->latest();
    }

    public function addGiftCardCodes($giftCardCodesId, $orderId)
    {
        $table = app('db')
            ->table('orders_gift_cards_codes');

        // check if the codes is available for concurrent users assigned
        if (is_null($table->where('gift_cards_codes_id', $giftCardCodesId)
            ->first())) {

            // Assign the codes to order
            $table
                ->insert([
                    'order_id'              => $orderId,
                    'gift_cards_codes_id'   => $giftCardCodesId,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);

//            \Log::info('Automated Assigning the code to order : '. $orderId, (array) app('db')
//                ->table('gift_cards_codes')
//                ->where('gift_cards_codes.id', $giftCardCodesId)->first());

            try {
                app(HistoryService::class)->create(null, [
                    'title' => 'Automatic Code Assignment',
                    'information' => "Automated Assigning the code to order : <a href='" . route('admin.order.view.action', $orderId) . "'>{$orderId}</a>",
                    'historyable_type' => GiftCardsCode::class,
                    'historyable_id' => $giftCardCodesId,
                    'type' => History::TYPE_AUTOMATIC_ASSIGN
                ]);
            } catch (\Exception $e){
                \Log::info('Failed to Create History for ' . 'Automated Assigning the code ' . $giftCardCodesId . 'to order : '. $orderId);
            }

            // Codes will be used for the given order
            app('db')
                ->table('gift_cards_codes')
                ->where('gift_cards_codes.id', $giftCardCodesId)
                ->update([
                    'is_used' => 1,
                ]);

            // Back Up the codes after used
            if ($order = $this->model->where('id', $orderId)->first()) {
                $order->update([
                    'assigned_order_item' => array_merge((array) $order->assigned_order_item, (array) app('db')
                        ->table('gift_cards_codes')
                        ->where('gift_cards_codes.id', $giftCardCodesId)->value('codes'))
                ]);
            }
        }
    }

    public function addTopUpAmounts($order)
    {
        $topUpAmount = app('db')
            ->table('game_top_ups_amounts')
            ->select(
                'game_top_ups_amounts.*',
                'top_up.slug as top_up_slug',
                'top_up.name as top_up_name',
                'top_up.image as top_up_image'
            )
            ->leftJoin (
                'game_top_ups as top_up',
                'top_up.id',
                '=',
                'game_top_ups_amounts.game_top_ups_id'
            )
            ->where('game_top_ups_amounts.id', $order->order_type_id)
            ->first();

        if ($topUpAmount) {
            $order->update([
                'metas' => array_merge((array) $order->metas, [
                    'top_up_amount_id' => $topUpAmount->id,
                ]),
            ]);
        }
    }

    public function getTotalAmountEarned($date = null)
    {
        $builder = app('db')
            ->table('orders')
            ->selectRaw('sum(quantity * amount) as total_amount_earned');

        if ($date) { // @todo delivered_status
            $builder->whereDate('updated_at', $date);
        }
        return $builder->first();
    }

    /**
     * Get order Stats for given date or period
     *
     * @param null $dailyDate
     * @param null $startDate
     * @param null $endDate
     * @param bool $range
     * @return mixed
     */
    public function getTotalOrderStats($dailyDate = null, $startDate = null, $endDate = null, $range = false)
    {
        $builder = app('db')
            ->table('orders')

            // Total Orders Count
            ->selectRaw('count(*) as total_exact_order')
            ->selectRaw('sum(quantity) as total_order')
            ->selectRaw('sum(quantity * amount) as total_amount_earned')

            // Count Orders by Order Type: Gift Card, Top-up etc
            ->selectRaw("sum(case when order_type = '".Product::PRODUCT_TOP_UP_INDEX."' then quantity end) as total_top_up")
            ->selectRaw("sum(case when order_type = '".Product::PRODUCT_TOP_UP_INDEX."' then (quantity * amount) end) as total_top_up_amount")
            ->selectRaw("sum(case when order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' then quantity end) as total_gift_card")
            ->selectRaw("sum(case when order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' then (quantity * amount) end) as total_gift_card_amount")

            // Count Orders by Delivery Status: Pending, Completed, Cancelled
            ->selectRaw("count(case when delivery_status = '".OrderLib::ORDER_PENDING_STATUS."' then 1 end) as pending_orders")
            ->selectRaw("count(case when delivery_status = '".OrderLib::ORDER_PROCESSING_STATUS."' then 1 end) as processing_orders")
            ->selectRaw("count(case when delivery_status = '".OrderLib::ORDER_COMPLETED_STATUS."' then 1 end) as completed_orders")
            ->selectRaw("count(case when delivery_status = '".OrderLib::ORDER_CANCELLED_STATUS."' then 1 end) as cancelled_orders")
            ->where('delivery_status', OrderLib::ORDER_COMPLETED_STATUS); // Only Delivered one

        if(!$this->isAdmin()){
            $builder->join('transactions', 'orders.transaction_id', '=', 'transactions.id');
            $builder->where('transactions.picked_by', Auth::id());
        }

        if($range || $dailyDate){
            if ($dailyDate)
                $builder->whereDate('orders.updated_at', $dailyDate);
            else{
                if($startDate)
                    $builder->whereDate('orders.updated_at', '>=', $startDate);
                if($endDate)
                    $builder->whereDate('orders.updated_at', '<=', $endDate);
            }
        }
         return $builder->first();
    }

    public function getStatsTypeWise($date)
    {
        $builder = app('db')
            ->table('orders')
            ->leftJoin('transactions', 'transactions.id', '=', 'orders.transaction_id')
            ->selectRaw('count(*) as total_exact_order');

        foreach (PaymentConst::gateways() as $gateway => $slug) {
            $paymentSlug = Utility::toSlug($slug, '_'); //imepay, prabhu_pay
            // Total Orders Count
            $builder = $builder
                ->selectRaw("SUM(CASE WHEN orders.delivery_status = '". OrderLib::ORDER_COMPLETED_STATUS."' AND transactions.payment_gateway_id = '".$gateway."' THEN orders.quantity ELSE 0 END) AS total_paid_order_of_" .$paymentSlug)
                ->selectRaw("SUM(CASE WHEN orders.delivery_status = '". OrderLib::ORDER_COMPLETED_STATUS."' AND transactions.payment_gateway_id = '".$gateway."' THEN orders.quantity * amount ELSE 0 END) AS total_amount_earned_".$paymentSlug)

                // Count Orders by Order Type: Gift Card, Top-up etc
                ->selectRaw("sum(case when orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '". OrderLib::ORDER_COMPLETED_STATUS."' then orders.quantity end) as total_top_up_".$paymentSlug)
                ->selectRaw("sum(case when orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND transactions.status ='".PaymentConst::PAYMENT_STATUS_PENDING."' then orders.quantity end) as total_pending_top_up_".$paymentSlug)
                ->selectRaw("sum(case when orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '". OrderLib::ORDER_COMPLETED_STATUS."' then (orders.quantity * orders.amount) end) as total_top_up_amount_".$paymentSlug)
                ->selectRaw("sum(case when orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '". OrderLib::ORDER_COMPLETED_STATUS."' then orders.quantity end) as total_gift_card_".$paymentSlug)
                ->selectRaw("sum(case when orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND transactions.status ='".PaymentConst::PAYMENT_STATUS_PENDING."' then orders.quantity end) as total_pending_gift_card_".$paymentSlug)
                ->selectRaw("sum(case when orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '". OrderLib::ORDER_COMPLETED_STATUS."' then (orders.quantity * orders.amount) end) as total_gift_card_amount_".$paymentSlug)

                // Count Orders by Delivery Status: Pending, Completed, Cancelled
                ->selectRaw("count(case when orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '".OrderLib::ORDER_PENDING_STATUS."' then 1 end) as total_pending_topup_orders_".$paymentSlug)
                ->selectRaw("count(case when orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '".OrderLib::ORDER_PENDING_STATUS."' then 1 end) as total_pending_gift_card_orders_".$paymentSlug)
                ->selectRaw("count(case when orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '".OrderLib::ORDER_COMPLETED_STATUS."' then 1 end) as total_completed_gift_card_orders_".$paymentSlug)
                ->selectRaw("count(case when orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' AND transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '".OrderLib::ORDER_COMPLETED_STATUS."' then 1 end) as total_completed_top_up_orders_".$paymentSlug);
//            ->where('orders.delivery_status', \Foundation\Lib\Order::ORDER_COMPLETED_STATUS) // Only Delivered one
        }

        $builder = $builder->where(function ($query) use ($date) {
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
                }
            })
            ->first();
        return $builder;
    }

    /**
     * Get the order stats by type for given duration
     *
     * @param $dailyDate
     * @param $startDate
     * @param $endDate
     * @return Collection
     */
    public function getOrderStatsForAPeriod($dailyDate, $startDate, $endDate)
    {
        $query = app('db')
            ->table('orders')
            ->where(function ($query) use ($dailyDate, $startDate, $endDate){
                if($dailyDate)
                    $query->whereDate('orders.created_at', $dailyDate);
                else
                    $query->whereBetween('orders.created_at', [$startDate, $endDate]);
            })
            ->selectRaw('DATE_FORMAT(orders.created_at, "%Y-%m-%d") as date')
            ->selectRaw("sum(case when order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' then quantity end) as total_gift_card")
            ->selectRaw("sum(case when order_type = '".Product::PRODUCT_TOP_UP_INDEX."' then quantity end) as total_top_up")
            ->groupBy('date')
            ->orderBy('date', 'desc');

        if(!$this->isAdmin()){
            $query->join('transactions', 'orders.transaction_id', '=', 'transactions.id');
            $query->where('transactions.picked_by', Auth::id());
        }

        return $query->get();
    }

    /**
     * Get Sales Amount for Given Duration
     *
     * @param $dailyDate
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getOrderAmountsForAPeriod($dailyDate, $startDate, $endDate)
    {
        $query = app('db')
            ->table('orders')
            ->where(function ($query) use ($dailyDate, $startDate, $endDate){
                if($dailyDate)
                    $query->whereDate('orders.created_at', $dailyDate);
                else
                    $query->whereBetween('orders.created_at', [$startDate, $endDate]);
            })
            ->selectRaw('DATE_FORMAT(orders.created_at, "%Y-%m-%d") as date')
            ->selectRaw("sum(case when order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' then (quantity * amount) end) as total_gift_card") //  - discounted_amount
            ->selectRaw("sum(case when order_type = '".Product::PRODUCT_TOP_UP_INDEX."' then (quantity * amount) end) as total_top_up") // - discounted_amount
            ->where('delivery_status', OrderLib::ORDER_COMPLETED_STATUS) // Only Delivered one
            ->groupBy('date')
            ->orderBy('date', 'desc');

        if(!$this->isAdmin()){
            $query->join('transactions', 'orders.transaction_id', '=', 'transactions.id');
            $query->where('transactions.picked_by', Auth::id());
        }

        return $query->get();
    }

    /**
     * Get Sales Amount for Given Duration
     *
     * @param $date
     * @return mixed
     */
    public function getOrderPicked($date)
    {
        return $this->model
            ->whereBetween('orders.created_at', [$date, now()])
            ->selectRaw('DATE_FORMAT(orders.created_at, "%Y-%m-%d") as date')
            ->selectRaw("SUM(CASE WHEN order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' THEN quantity END) AS total_gift_card")
            ->selectRaw("SUM(CASE WHEN order_type = '".Product::PRODUCT_TOP_UP_INDEX."' THEN quantity - discounted_amount END) AS total_top_up")
            ->selectRaw("COUNT(CASE WHEN order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' AND delivery_status = '".OrderLib::ORDER_COMPLETED_STATUS."' THEN 1 END) AS completed_gift_card_orders")
            ->selectRaw("COUNT(CASE WHEN order_type = '".Product::PRODUCT_TOP_UP_INDEX."' AND delivery_status = '".OrderLib::ORDER_COMPLETED_STATUS."' THEN 1 END) AS completed_top_up_orders")
            ->join('transactions', 'orders.transaction_id', '=', 'transactions.id')
            ->where('transactions.picked_by', auth()->id())
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Get Order count grouped by picker
     *
     * @param array $data
     * @return mixed
     */
    public function getStatsGroupedByPicker(array $data)
    {
        $startDate = $data['created_at']['from'] ?? Meta::get('initial_date_from') ?? today()->format('Y-m-d');
        $endDate = $data['created_at']['to'] ?? Meta::get('initial_date_to') ?? Carbon::today()->toDateString();

        return app('db')
            ->table('orders')
            ->selectRaw('CONCAT_WS(" ", users.first_name, users.middle_name, users.last_name) AS full_name')
            ->selectRaw("count(case when delivery_status = '".OrderLib::ORDER_PENDING_STATUS."' then 1 end) as pending_orders")
            ->selectRaw("count(case when delivery_status = '".OrderLib::ORDER_PROCESSING_STATUS."' then 1 end) as processing_orders")
            ->selectRaw("count(case when delivery_status = '".OrderLib::ORDER_COMPLETED_STATUS."' then 1 end) as completed_orders")
            ->selectRaw("count(case when delivery_status = '".OrderLib::ORDER_CANCELLED_STATUS."' then 1 end) as cancelled_orders")
            ->selectRaw("MAX(case when delivery_status = '".OrderLib::ORDER_COMPLETED_STATUS."' then orders.created_at end) as created_at")
            ->join('transactions', 'orders.transaction_id', '=', 'transactions.id')
            ->join('users', 'transactions.picked_by', '=', 'users.id')
            ->groupBy('users.first_name', 'users.middle_name', 'users.last_name', 'users.id')
            ->whereDate('orders.created_at', '>=', $startDate)
            ->whereDate('orders.created_at', '<=', $endDate)
            ->when(!auth()->user()->isAdmin(), function ($query) {
                if (auth()->user()->seeOnlyYours()) {
                    $query->where('transactions.picked_by', auth()->id());
                }
            })
            ->get();
    }

    /**
     * Get The Picker ID from given order ID
     * Return false when Order not Found, Return null if Order Exists but it is not picked by anyone. i.e 'transactions.picked_by' == null
     *
     * @param $orderId
     * @return bool
     */
    public function getPickerId($orderId)
    {
        if($order = $this->model->where('order_id', $orderId)->first()){
            return Payment::firstWhere('id', $order->id)->picked_by;
        }
        return false;
    }

    /**
     * Get User who ordered from Order ID
     *
     * @param $orderId
     * @return Builder|Builder[]|Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function getUser($orderId)
    {
        $order = $this->model->with(['transaction:id,transaction_id,user_id', 'transaction.user:id,first_name,middle_name,last_name,email'])->find($orderId);
        return $order->transaction->user;
    }

    /**
     * Check if currently logged in user is Admin or Super-Admin
     *
     * @return bool
     */
    private function isAdmin()
    {
        $user = Auth::user();
        if($user->hasRole('admin', 'super-admin')){
            return true;
        }
        return false;
    }

}

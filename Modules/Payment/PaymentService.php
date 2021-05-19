<?php

namespace Modules\Payment;

use Carbon\Carbon;
use Foundation\Lib\Date;
use Foundation\Lib\Order as OrderConst;
use Foundation\Lib\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Foundation\Builders\Filters\Order\Filter;
use Foundation\Builders\Filters\Order\RedeemFilter;
use Foundation\Builders\Filters\Order\PickedFilter;
use Modules\Payment\Libs\Payment as PaymentConst;
use Neputer\Config\Status;
use Neputer\Supports\Utility;

/**
 * Class PaymentService
 * @package Modules\Payment
 */
final class PaymentService
{

    /**
     * @var Payment
     */
    private $payment;

    /**
     * PaymentService constructor.
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function create(array $data)
    {
        return $this->payment->create($data);
    }

    public function find($id)
    {
        return $this->payment->find($id);
    }

    public function findByReferenceId($referenceId)
    {
        return $this->payment->where('reference_id', $referenceId)->first();
    }

    public function updateStatus($id, array $data)
    {
        $payment = $this->find($id);
        $payment->update($data);
        return $payment;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data)
    {
        return Filter::apply($this->payment
            ->select(
                'transactions.id',
                'transactions.transaction_id',
                'transactions.payment_gateway_id',
                'transactions.picked_by',
                'transactions.user_id',
                'transactions.created_at',
                'transactions.is_delivered',
                'transactions.status'
            )
            ->with('orders', 'user', 'picker')
            ->with('unreadConversationsByAdmin:id,transaction_id')
            ->orderBy('transactions.created_at', 'DESC'), $data);
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filterPickedByMyself(array $data)
    {
        return PickedFilter::apply($this->payment
            ->select(
                'transactions.id',
                'transactions.transaction_id',
                'transactions.payment_gateway_id',
                'transactions.picked_by',
                'transactions.created_at',
                'transactions.user_id',
                'transactions.status',
                'users.first_name', 'users.middle_name', 'users.last_name'
            )
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->with('orders', 'user', 'picker'), $data)
            ->orderBy('transactions.created_at', 'DESC');
    }

    public function filterRedeem(array $data)
    {
        return RedeemFilter::apply($this->payment
            ->select(
                'transactions.id',
                'transactions.transaction_id',
                'transactions.payment_gateway_id',
                'transactions.picked_by',
                'transactions.created_at',
                'transactions.user_id',
                'transactions.status',
                'users.first_name', 'users.middle_name', 'users.last_name'
            )
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->with('orders', 'user', 'picker'), $data)
            ->orderBy('transactions.created_at', 'DESC')
            ->where('redeem', 1);
    }

    public function byUser($userId, $filter, $status = \Modules\Payment\Libs\Payment::PAYMENT_STATUS_DELIVERED, $time = false)
    {
        $builder = $this->payment
            ->select(
                'transactions.*',
                'transactions.transaction_id as transaction_code'
            )
            ->selectRaw('CONCAT_WS(" ", users.first_name, users.middle_name, users.last_name) AS full_name')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->withCount('orders')
            ->with('orders')
            ->withCount('unreadConversations')
            ->where(function ($query) use ($filter){
                if($filter){
                    $query->where('transactions.transaction_id', $filter);
                }
            })
            ->where('transactions.user_id', $userId)
            ->where('transactions.status', $status);

        if ($time) {
//            Carbon::setWeekStartsAt(Carbon::SUNDAY);
//            Carbon::setWeekEndsAt(Carbon::SATURDAY);
            $builder = $builder
                ->whereBetween('transactions.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }

//            ->whereRaw(('case WHEN transactions.payment_gateway_id = '.\Modules\Payment\Libs\Payment::PAYMENT_GATEWAY_OTHER.' THEN transactions.is_notified = 0 ELSE transactions.is_notified = 1 END'))
        return $builder->orderBy('transactions.created_at', 'DESC')
            ->limit(50)
            ->get();
    }

    public function ofUser($userId, $filter, $limit = 10)
    {
        $keyword = Arr::get($filter, 'keyword');
        $sortBy  = Arr::get($filter, 'sortBy') === 'Ascending' ? 'ASC' : 'DESC';
        return $this->payment
            ->select(
                'transactions.*',
                'transactions.transaction_id as transaction_code'
            )
            ->selectRaw('CONCAT_WS(" ", users.first_name, users.middle_name, users.last_name) AS full_name')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->with('orders')
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('transactions.transaction_id', $keyword);
            })
            ->where('transactions.user_id', $userId)
            ->where('transactions.status', \Modules\Payment\Libs\Payment::PAYMENT_STATUS_DELIVERED)
            ->orderBy('transactions.created_at', $sortBy)
            ->simplePaginate($limit);
    }

    public function totalOfUser($userId, $filter)
    {
        $keyword = Arr::get($filter, 'keyword');
        $sortBy  = Arr::get($filter, 'sortBy') === 'Ascending' ? 'ASC' : 'DESC';

        return $this->payment
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('transactions.transaction_id', $keyword);
            })
            ->where('transactions.user_id', $userId)
            ->where('transactions.status', \Modules\Payment\Libs\Payment::PAYMENT_STATUS_DELIVERED)
            ->orderBy('transactions.created_at', $sortBy)
            ->count('transactions.id');
    }

    public function byTransactionIdentifier($transactionId)
    {
        return $this->payment
            ->select(
                'transactions.*',
                'transactions.transaction_id as transaction_code',
                'transactions.payment_gateway_id'
            )
            ->with('orders')
            ->selectRaw('CONCAT_WS(" ", users.first_name, users.middle_name, users.last_name) AS full_name')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->withCount('orders')
            ->where('transactions.transaction_id', $transactionId)
            ->first();
    }

    public function byTransactionId($transactionId, $userId)
    {
        return $this->payment
            ->select(
                'transactions.*',
                'transactions.transaction_id as transaction_code',
                'transactions.payment_gateway_id'
            )
            ->with('orders')
            ->with([
                'conversations' => function ($query) {
                    $query->with('author:id,image')
                        ->orderBy('created_at', 'DESC')
                        ->limit(10);
                }
            ])
            ->selectRaw('CONCAT_WS(" ", users.first_name, users.middle_name, users.last_name) AS full_name')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.transaction_id', $transactionId)
            ->first();
    }

    public function getBankAttrs($transactionId)
    {
        return $this->payment
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->selectSub(app('db')
                ->table('transactions_bank_orders')
                ->select('transactions_bank_orders.bank_id  as bank_id')
                ->whereColumn('transactions_bank_orders.transaction_id', 'transactions.id')
                ->limit(1)
                ->toSql(), 'bank_id')
            ->selectSub(app('db')
                ->table('transactions_bank_orders')
                ->select('banks.name  as bank_name')
                ->leftJoin('banks', 'banks.id', 'transactions_bank_orders.bank_id')
                ->whereColumn('transactions_bank_orders.transaction_id', 'transactions.id')
                ->limit(1)
                ->toSql(), 'bank_name')
            ->selectSub(app('db')
                ->table('transactions_bank_orders')
                ->select('transactions_bank_orders.voucher as bank_voucher')
                ->whereColumn('transactions_bank_orders.transaction_id', 'transactions.id')
                ->limit(1)
                ->toSql(), 'bank_voucher')
            ->where('transactions.id', $transactionId)
            ->first();
    }

    /**
     * Get List of Transactions having unread messages
     *
     * @param $userId
     * @return mixed
     */
    public function getUnreadMessages($userId)
    {
        return $this->payment
            ->select(
                'transactions.id', 'transactions.transaction_id', 'transactions.user_id', 'transactions.created_at'
            )
            ->where('transactions.user_id', $userId)
            ->has('unreadConversations')
            ->orderBy('transactions.created_at', 'DESC')
            ->limit(50)
            ->get();
    }

    /**
     * Get Transaction with orders from given list of ids
     *
     * @param $ids
     * @return Collection
     */
    public function getUnpickedFromId($ids)
    {
        return $this->payment->whereIntegerInRaw('id', (array) $ids)->whereNull('picked_by')->with('orders')->get();
    }

    public function getFromIds($ids)
    {
        return $this->payment
            ->whereIntegerInRaw('id', (array) $ids)
            ->with([
                'orders' => function ($query) {
                    return  $query->where('delivery_status', Order::ORDER_COMPLETED_STATUS);
                }
            ])
            ->get();
    }

    public function query()
    {
        return $this->payment->newQuery();
    }

    public function sortByUse($date, $sort = 'ASC'): array
    {
        $data = $this->payment->query();

        $date = request()->get('date-type') ?? Date::DATE_TODAY;
        $gatewayIndex = request()->get('gateway-type') ?? 'all';
        $type = request()->get('product-type') ?? 'all';
        $dateRange = explode(' - ', request()->get('date-range'));

        foreach (PaymentConst::gateways() as $gateway => $slug) {
            $data = $data
                ->selectRaw("SUM(case when transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '".OrderConst::ORDER_COMPLETED_STATUS."' AND orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' then orders.quantity end) AS ". "paid_top_up_".Utility::toSlug($slug, '_'))
                ->selectRaw("SUM(case when transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '".OrderConst::ORDER_PENDING_STATUS."' AND orders.order_type = '".Product::PRODUCT_TOP_UP_INDEX."' then orders.quantity end) AS ". "unpaid_top_up_".Utility::toSlug($slug, '_'))
                ->selectRaw("SUM(case when transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '".OrderConst::ORDER_COMPLETED_STATUS."' AND orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' then orders.quantity end) AS ". "paid_gift_card_".Utility::toSlug($slug, '_'))
                ->selectRaw("SUM(case when transactions.payment_gateway_id = '".$gateway."' AND orders.delivery_status = '".OrderConst::ORDER_PENDING_STATUS."' AND orders.order_type = '".Product::PRODUCT_GIFT_CARD_INDEX."' then orders.quantity end) AS ". "unpaid_gift_card_".Utility::toSlug($slug, '_'));
        }

        $data = $data
            ->leftJoin('orders', 'orders.transaction_id', '=', 'transactions.id')
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
            ->when(!is_null($gatewayIndex), function ($query) use ($gatewayIndex) {
                if ($gatewayIndex === 'all') {
                    $query->whereIntegerInRaw('transactions.payment_gateway_id', array_keys(PaymentConst::gateways()));
                } else {
                    $query->where('transactions.payment_gateway_id', $gatewayIndex);
                }
            })
            ->when(!is_null($type), function ($query) use ($type) {
                if ($type === 'all') {
                    $query->whereIntegerInRaw('orders.order_type', array_keys(Product::getTypes()));
                } else {
                    $query->where('orders.order_type', $type);
                }
            })
//            ->where('status', PaymentConst::PAYMENT_STATUS_DELIVERED)
            ->first()
            ->toArray();

        return $data;
//        return Arr::sort($data);
    }

}

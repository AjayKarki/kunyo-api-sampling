<?php

namespace Foundation\Services;

use Foundation\Lib\Meta;
use Foundation\Models\Order;
use Foundation\Models\Accounting;
use Illuminate\Support\Arr;
use Neputer\Supports\BaseService;

/**
 * Class AccountingService
 * @package Foundation\Services
 */
class AccountingService extends BaseService
{

    /**
     * The Accounting instance
     *
     * @var $model
     */
    protected $model;
    /**
     * @var Order
     */
    private $order;

    /**
     * AccountingService constructor.
     * @param Accounting $accounting
     * @param Order $order
     */
    public function __construct(Accounting $accounting, Order $order)
    {
        $this->model = $accounting;
        $this->order = $order;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function  filter(array $data)
    {
        $query = $this->order->newQuery();

        $query->select('id', 'amount as selling_price', 'quantity', 'discount', 'discounted_amount', 'original_price', 'transaction_id')
            ->with('transaction:id,service_charge,transaction_id')
            ->where('delivery_status', \Foundation\Lib\Order::ORDER_COMPLETED_STATUS)
            ->orderby('created_at', 'DESC');

        if ($searchKey = Arr::get($data, 'search.value')) {
            $query->where('transaction_id', 'like', '%' . $searchKey . '%');
        }

        if ($startDate = Arr::get($data, 'filter.startDate')) {
            $query->where('orders.created_at', '>=', $startDate);
        }

        if ($endDate = Arr::get($data, 'filter.endDate')) {
            $query->where('orders.created_at', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Get the Accounting statistics
     *
     * @param array $filter
     * @return mixed
     */
    public function getStats(array $filter = [])
    {
        $startDate = $filter['startDate'] ?? null;
        $endDate = $filter['endDate'] ?? null;
        $dailyDate = $filter['dailyDate'] ?? null;

        $query = $this->order->query();

        if($dailyDate)
            $query->whereDate('created_at', $dailyDate);
        else{
            if($startDate)
                $query->whereDate('created_at', '>=', $startDate);
            if($endDate)
                $query->whereDate('created_at', '<=', $endDate);
        }

        $query->selectRaw('SUM((CASE WHEN original_price = "0" THEN amount ELSE original_price END) * quantity) as original_amount');
        $query->selectRaw('SUM(amount * quantity) as selling_amount');
        $query->where('delivery_status', \Foundation\Lib\Order::ORDER_COMPLETED_STATUS);

        $orders = $query->first();

        $profit = $orders->selling_amount - $orders->original_amount;

        $data['profit_amt'] = $profit;

        $data['total'] = nrp($orders->selling_amount ?? 0);
        $data['profit'] = nrp($profit, 2);

        if($orders->original_price == 0)
            $data['profit_percent'] = 0;
        else
            $data['profit_percent'] = round($profit/$orders->original_amount * 100, 2);

        return $data;
    }

}

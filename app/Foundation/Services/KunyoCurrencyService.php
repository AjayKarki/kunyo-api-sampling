<?php

namespace Foundation\Services;

use Foundation\Models\KunyoCurrency;
use Illuminate\Support\Arr;
use Modules\Payment\Libs\Payment;
use Neputer\Supports\BaseService;

/**
 * Class KunyoCurrencyService
 * @package Foundation\Services
 */
class KunyoCurrencyService extends BaseService
{

    /**
     * The KunyoCurrency instance
     *
     * @var $model
     */
    protected $model;

    /**
     * KunyoCurrencyService constructor.
     * @param KunyoCurrency $kunyoCurrency
     */
    public function __construct(KunyoCurrency $kunyoCurrency)
    {
        $this->model = $kunyoCurrency;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data = [])
    {
        $query = $this->model->newQuery();

        if($search = Arr::get($data, 'search.value')){
            $query->where(function ($query) use ($search){
                $query->where('order_id','like', '%'. $search .'%');
                $query->orWhere('user_name','like', '%'. $search .'%');
            });
        }

        if ($transactionCode = Arr::get($data, 'filter.transaction_code')) {
            $query->where('order_id', 'like', '%' . $transactionCode . '%');
        }

        if ($paymentStatus = Arr::get($data, 'filter.payment_status')) {
            switch ($paymentStatus) {
                case "paid":
                    $query->where('payment_status', Payment::PAYMENT_STATUS_DELIVERED);
                    break;
                case "processing":
                    $query->where('payment_status', Payment::PAYMENT_STATUS_PROCESSING);
                    break;
                case "unpaid":
                    $query->where('payment_status', Payment::PAYMENT_STATUS_PENDING);
                    break;
            }
        }

        if (! is_null($gateway = Arr::get($data, 'filter.gateway'))) {
            $query->where('payment_gateway_id', $gateway);
        }

        if ($status = Arr::get($data,'filter.status')) {
            switch ($status) {
                case "pending":
                    $query->where('delivery_status', 0);
                    break;
                case "completed":
                    $query->where('delivery_status', 1);
                    break;
            }
        }

        if ($createdFrom = Arr::get($data,'filter.createdAt.from')) {
            $query->whereDate('created_at', '>=', $createdFrom);
        }

        if ($createdTo = Arr::get($data,'filter.createdAt.to')) {
            $query->whereDate('created_at', '<=', $createdTo);
        }

        return $query->latest();
    }

    /**
     * Get first model that matches the $condition
     *
     * @param $condition
     * @return mixed
     */
    public function firstWhere($condition)
    {
        return $this->model->where($condition)->first();
    }

    /**
     * Get Currency Orders for a User
     *
     * @param $userId
     * @return mixed
     */
    public function getByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->where('payment_status', \Modules\Payment\Libs\Payment::PAYMENT_STATUS_DELIVERED)->latest()->get();
    }

    /**
     * Get List of Currency Buyers (Users)
     *
     * @param $search
     * @return mixed
     */
    public function filterOwners($search)
    {
        $query = $this->model
            ->select(
                'kunyo_currency.created_at as last_order_date', 'kunyo_currency.user_name',
                'users.id as user_id', 'users.kunyo_currency'
            )
            ->join('users', 'users.id', '=', 'kunyo_currency.user_id');

        if ($search)
            $query->where('user_name', 'like', '%' . $search . '%');

        return $query->orderBy('last_order_date', 'DESC');
    }

    /**
     * Total Number of Currency Sold Till Date
     *
     * @param $userId
     * @param null $dailyDate
     * @param null $startDate
     * @param null $endDate
     * @param bool $range
     * @return \Illuminate\Database\Eloquent\Builder|int|mixed
     */
    public function salesStats($userId = null, $dailyDate = null, $startDate = null, $endDate = null, $range = false)
    {
        $query = $this->model->newQuery();
        if ($userId)
            return $query->where('user_id', $userId);

        $query->selectRaw('sum(quantity) as quantity');
        $query->selectRaw('sum(amount - service_charge) as revenue');
        $query->where('payment_status', \Modules\Payment\Libs\Payment::PAYMENT_STATUS_DELIVERED);

        if($range || $dailyDate){
            if ($dailyDate)
                $query->whereDate('updated_at', $dailyDate);
            else{
                if($startDate)
                    $query->whereDate('updated_at', '>=', $startDate);
                if($endDate)
                    $query->whereDate('updated_at', '<=', $endDate);
            }
        }
        return $query->first();
    }

}

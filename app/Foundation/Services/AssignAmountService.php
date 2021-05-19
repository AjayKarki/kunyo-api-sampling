<?php

namespace Foundation\Services;

use App\Foundation\Lib\AssignAmount as AssignType;
use Carbon\Carbon;
use Foundation\Lib\Order;
use Foundation\Lib\Product;
use Foundation\Models\AssignAmount;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Neputer\Supports\BaseService;

/**
 * Class AssignAmountService
 * @package Foundation\Services
 */
class AssignAmountService extends BaseService
{

    /**
     * The AssignAmount instance
     *
     * @var $model
     */
    protected $model;

    /**
     * AssignAmountService constructor.
     * @param AssignAmount $assignAmount
     */
    public function __construct(AssignAmount $assignAmount)
    {
        $this->model = $assignAmount;
    }

    /**
     * Filter
     *
     * @param array $data
     * @return mixed
     */
    public function filter(array $data)
    {
        $query = $this->model->newQuery();

        $query->select(
            DB::raw('MAX(CASE WHEN type ="' . AssignType::TYPE_CREDIT . '" then assign_amounts.created_at end) as last_assigned_date'),
            DB::raw('SUM(credit) as assigned_amount'),
            DB::raw('SUM(debit) as used_amount'),
            DB::raw('SUM(credit - debit) as remaining_amount'),
            DB::raw('MAX(user_id) as user_id'),
            DB::raw('SUM(CASE WHEN order_type = "'. Product::PRODUCT_GIFT_CARD_INDEX .'" then debit end) as gift_card_spend'),
            DB::raw('SUM(CASE WHEN order_type = "'. Product::PRODUCT_TOP_UP_INDEX .'" then debit end) as top_up_spend')
        );

        $query->join('users', 'users.id', '=', 'assign_amounts.user_id');

        if ($name = Arr::get($data, 'search.value'))
            $query->whereRaw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name,"")) like ?', ['%' . $name . '%']);

        if ($date = Arr::get($data, 'filter.created_at'))
            $query->whereDate('assign_amounts.created_at', $date);

        $query->orderBy('last_assigned_date', 'desc');
        $query->with('assignee:id,first_name,last_name,email');

        return $query->groupBy('user_id');
    }

    /**
     * Get Assigned Amounts by User ID
     *
     * @param $userId
     * @param null $startDate
     * @param null $endDate
     * @return mixed
     */
    public function findByUserId($userId, $startDate = null, $endDate = null)
    {
        $query = $this->model
            ->where('user_id', $userId);

        if ($startDate)
            $query->whereDate('created_at', '>=', $startDate);

        if ($endDate)
            $query->whereDate('created_at', '<=', $endDate);

        $result = $query->get();

        return $result->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('Y-m-d');
        })->sortKeysDesc();
    }

    /**
     * Insert Debit Amounts
     *
     * @param $amounts
     * @return mixed
     */
    public function insert($amounts)
    {
        $data = [];
        foreach ($amounts as $transaction) {
            foreach ($transaction as $amount) {
                array_push($data, [
                    'debit' => $amount['amount'],
                    'type' => \App\Foundation\Lib\AssignAmount::TYPE_DEBIT,
                    'order_id' => $amount['order_id'],
                    'order_type' => $amount['order_type'],
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return $this->model->insert($data);
    }

    public function insertDebitAmount($order)
    {
        /**
         * One at a time
         * ($order->quantity * $order->amount) - $order->discounted_amount
         */
        $discountAmount = $order->discounted_amount ?? 0;
        return $this->model->create([
            'debit'      => $order->quantity > 1
                ? ($order->amount - ($discountAmount / $order->quantity)) :
                ($order->quantity * $order->amount) - $discountAmount,
            'type'       => \App\Foundation\Lib\AssignAmount::TYPE_DEBIT,
            'order_id'   => $order->id,
            'order_type' => $order->order_type,
            'user_id'    => auth()->id(),
        ]);
    }

    /**
     * Check if a credit entry exists for a given user
     *
     * @return mixed
     */
    public function creditExists($userId)
    {
        return $this->model->where('user_id', $userId)->where('type', AssignType::TYPE_CREDIT)->exists();
    }


    /**
     * Delete all records of a User
     *
     * @param $userId
     * @return bool
     */
    public function delete($userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }
}

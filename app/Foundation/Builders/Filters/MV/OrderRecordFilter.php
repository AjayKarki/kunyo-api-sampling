<?php

namespace Foundation\Builders\Filters\MV;

use Foundation\Lib\Order;
use Foundation\Lib\Role;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Modules\Payment\Libs\Payment;

class OrderRecordFilter
{

    /**
     * @param Builder $builder
     * @param array $data
     * @return Builder
     */
    public static function apply(Builder $builder, array $data)
    {
        $builder = $builder->newQuery();

        $searchKey = Arr::get($data, 'search.value');

        if($searchKey) {

            $checker = (int) $searchKey;
            if ($checker > 0) {
                $builder->where(
                    'transaction_id',
                    'like',
                    '%'.$searchKey.'%'
                );
            } else {

                $builder
                    ->where(
                        'customer_full_name',
                        'like',
                        '%'.$searchKey.'%');

            }
        }

        if (! is_null($gateway = Arr::get($data, 'filter.gateway'))) {
            $builder->where('payment_gateway_id', $gateway);
        }

        $createdFrom = Arr::get($data,'filter.createdAt.from');
        $createdTo = Arr::get($data,'filter.createdAt.to');

        if ($createdFrom) {
            $builder->whereDate('created_at', '>=', $createdFrom);
        }

        if ($createdTo) {
            $builder->whereDate('created_at', '<=', $createdTo);
        }

        $status = Arr::get($data,'filter.status');

        if ($status) {

            switch ($status) {
                case "pending":
                    $builder->where('order_is_delivered', Order::ORDER_PENDING_STATUS);
                    break;
                case "completed":
                    $builder->where('order_is_delivered', Order::ORDER_COMPLETED_STATUS);
                    break;
            }
        }

        $paymentStatus = Arr::get($data, 'filter.payment_status');

        if ($paymentStatus) {
            switch ($paymentStatus) {
                case "paid":
                    $builder->where('status', Payment::PAYMENT_STATUS_DELIVERED);
                    break;
                case "processing":
                    $builder->where('status', Payment::PAYMENT_STATUS_PROCESSING);
                    break;
                case "unpaid":
                    $builder->where('status', Payment::PAYMENT_STATUS_PENDING);
                    break;
            }
        }

        if (!auth()->user()->hasRole(Role::$current[Role::ROLE_SUPER_ADMIN])) {
            $builder->where(function ($query) {
                $query->whereNull('picked_by')->orWhere('picked_by', auth()->id());
            });
        }

        return $builder;
    }

}

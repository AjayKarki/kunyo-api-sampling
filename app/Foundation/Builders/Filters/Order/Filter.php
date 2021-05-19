<?php

namespace Foundation\Builders\Filters\Order;

use Foundation\Lib\Role;
use Foundation\Lib\Order;
use Illuminate\Support\Arr;
use Modules\Payment\Libs\Payment;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 * @package Foundation\Builders\Filters\Order
 */
final class Filter
{

    public static function apply(Builder $builder, array $data)
    {
        $builder = $builder->newQuery();

        $searchKey = Arr::get($data, 'search.value');

        if($searchKey) {

            $checker = (int) $searchKey;
            if ($checker > 0) {
                $builder->where(
                    'transactions.transaction_id',
                    'like',
                    '%'.$searchKey.'%'
                );
            } else {

                $builder->orWhereHas('user', function ($query) use ($searchKey){
                    $query->whereRaw(
                        "CONCAT_WS(' ', first_name, middle_name, last_name) LIKE ? ", ['%'.$searchKey.'%']);

                    $query->Orwhere(
                        'first_name',
                        'like',
                        '%'.$searchKey.'%'
                    )
                        ->orWhere(
                            'middle_name',
                            'like',
                            '%'.$searchKey.'%'
                        )
                        ->orWhere(
                            'last_name',
                            'like',
                            '%'.$searchKey.'%'
                        );
                });

            }

//
////            $builder->orWhereHas('orders', function ($subQuery) use($searchKey) {
////                $subQuery->where(
////                    'orders.transaction_id',
////                    'like',
////                    '%'.$searchKey.'%'
////                );
////            });
        }

        if ($transactionCode = Arr::get($data, 'filter.transaction_code')) {
            $builder->where('transactions.transaction_id', 'like', '%'.$transactionCode.'%');
            $builder->orWhereHas('orders', function ($query) use($transactionCode){
                $query->where('orders.transaction_id', 'like', '%'.$transactionCode.'%');
            });
        }

        if (! is_null($gateway = Arr::get($data, 'filter.gateway'))) {
            $builder->where('transactions.payment_gateway_id', $gateway);
        }

        $createdFrom = Arr::get($data,'filter.createdAt.from');
        $createdTo = Arr::get($data,'filter.createdAt.to');

        if ($createdFrom) {
            $builder->whereDate('transactions.created_at', '>=', $createdFrom);
        }

        if ($createdTo) {
            $builder->whereDate('transactions.created_at', '<=', $createdTo);
        }

        $status = Arr::get($data,'filter.status');

        if ($status) {
            switch ($status) {
                case "pending":
                    $builder->orWhereHas('orders', function ($query) {
                        $query->where('orders.delivery_status', Order::ORDER_PENDING_STATUS);
                    });
                    break;
                case "completed":
                    $builder->orWhereHas('orders', function ($query) {
                        $query->where('orders.delivery_status', Order::ORDER_COMPLETED_STATUS);
                    });
                    break;
            }
        }

        if ($name = Arr::get($data, 'name')) {
            $builder->orWhereHas('user', function ($subQuery) use($searchKey) {
                $subQuery->orWhereRaw(
                    'CONCAT(first_name, " ", last_name)',
                        'like',
                        '%'.$searchKey.'%'
                    );
            });
        }

        $paymentStatus = Arr::get($data, 'filter.payment_status');

        if ($paymentStatus && is_null($searchKey)) {
            switch ($paymentStatus) {
                case "paid":
                    $builder->where('transactions.status', Payment::PAYMENT_STATUS_DELIVERED);
                    break;
                case "processing":
                    $builder->where('transactions.status', Payment::PAYMENT_STATUS_PROCESSING);
                    break;
                case "unpaid":
                    $builder
                        ->whereBetween('transactions.created_at', [ now()->startOfWeek(), now()->endOfWeek() ])
                        ->where('transactions.status', Payment::PAYMENT_STATUS_PENDING);
                    break;
            }
        }

//        if (!auth()->user()->hasRole(Role::$current[Role::ROLE_SUPER_ADMIN])) {
//            $builder->where(function ($query) {
//                $query->whereNull('picked_by')->orWhere('picked_by', auth()->id());
//            });
//        }

        return $builder;
    }

}

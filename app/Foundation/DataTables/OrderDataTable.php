<?php

namespace Foundation\DataTables;

use Throwable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class OrderDataTable
 * @package Foundation\DataTables
 */
final class OrderDataTable
{

    /**
     * @param Builder $builder
     * @return mixed
     * @throws Throwable
     */
    public static function init(Builder $builder)
    {
        return datatables()
            ->of($builder)
            ->addIndexColumn()
            ->addColumn('transaction_id', function ($data) {
                $unread = '';
                $count = $data->unreadConversationsByAdmin->count();
                if($count > 0)
                    $unread = " <i class='fa fa-envelope text-success' title='{$count} new response(s) from Customer'></i>";
                return $data->transaction_id . $unread;
            })
            ->addColumn('full_name', function ($data) {
                if (request()->route()->getName() === 'admin.order.data-table') {
                    $fullName = join(' ', array_filter([
                        optional($data->user)->first_name,
                        optional($data->user)->middle_name,
                        optional($data->user)->last_name
                    ]));
                } else {

                $fullName = $data->customer_full_name;
                }
                return view(
                    'admin.order.partials.user-link',
                    compact('data', 'fullName')
                )->render();
            })
            ->addColumn('payment_gateway', function ($data) {
                return view(
                    'admin.order.partials.payment-gateway',
                    compact('data')
                )->render();
            })
            ->addColumn('delivery_status', function ($data) {
                return view(
                    'admin.order.partials.delivery-status',
                    compact('data')
                )->render();
            })
            ->addColumn('picked_by', function ($data) {
                return view(
                    'admin.order.partials.picked-by',
                    compact('data')
                )->render();
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->addColumn('orders_count', function ($data) {
                return view(
                    'admin.order.partials.order',
                    compact('data')
                )->render();
            })
            ->addColumn('action', function ($data) {
                $model = 'order';
                return view(
                    'admin.order.partials.data-table-action',
                    compact('data', 'model')
                )->render();
            })
//            ->addColumn('products', function ($data) {
//                return view(
//                    'admin.order.partials.products',
//                    compact('data')
//                )->render();
//            })
            ->addColumn('checkbox', function ($data) {
                return view('admin.common.checkbox', compact('data'))->render();
            })
            ->rawColumns([ 'transaction_id', 'action', 'orders_count', 'created_at', 'products', 'checkbox', 'payment_gateway', 'delivery_status', 'picked_by', 'full_name', ])
//            ->skipPaging()
            ->make(true);
    }

    /**
     * @param Builder $builder
     * @return mixed
     * @throws Throwable
     */
    public static function picked(Builder $builder)
    {
        return datatables()
            ->of($builder)
            ->addColumn('full_name', function ($data) {
                $fullName = join(' ', [
                    $data->first_name,
                    $data->middle_name,
                    $data->last_name
                ]);
                return view(
                    'admin.order.partials.user-link',
                    compact('data', 'fullName')
                )->render();
            })
            ->addColumn('payment_gateway', function ($data) {
                return view(
                    'admin.order.partials.payment-gateway',
                    compact('data')
                )->render();
            })
            ->addColumn('delivery_status', function ($data) {
                return view(
                    'admin.order.partials.delivery-status',
                    compact('data')
                )->render();
            })
            ->addColumn('picked_by', function ($data) {
                return view(
                    'admin.order.partials.picked-by',
                    compact('data')
                )->render();
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->addColumn('orders_count', function ($data) {
                return view(
                    'admin.order.partials.order',
                    compact('data')
                )->render();
            })
            ->addColumn('action', function ($data) {
                $model = 'order';
                return view(
                    'admin.order.partials.data-table-action',
                    compact('data', 'model')
                )->render();
            })
            ->addColumn('status', function ($data) {
                return view(
                    'admin.order.partials.status',
                    compact('data')
                )->render();
            })
            ->addColumn('checkbox', function ($data) {
                return view('admin.common.checkbox', compact('data'))->render();
            })
            ->rawColumns([ 'action', 'orders_count', 'created_at', 'status', 'checkbox', 'payment_gateway', 'delivery_status', 'picked_by', 'full_name', ])
            ->make(true);
    }

    /**
     * @param $builder
     * @return mixed
     * @throws Throwable
     */
    public static function redeem($builder)
    {
        return datatables()
            ->of($builder)
            ->addColumn('full_name', function ($data) {
                $fullName = join(' ', [
                    $data->first_name,
                    $data->middle_name,
                    $data->last_name
                ]);
                return view(
                    'admin.order.partials.user-link',
                    compact('data', 'fullName')
                )->render();
            })
            ->addColumn('payment_gateway', function ($data) {
                return view(
                    'admin.order.partials.payment-gateway',
                    compact('data')
                )->render();
            })
            ->addColumn('delivery_status', function ($data) {
                return view(
                    'admin.order.partials.delivery-status',
                    compact('data')
                )->render();
            })
            ->addColumn('picked_by', function ($data) {
                return view(
                    'admin.order.partials.picked-by',
                    compact('data')
                )->render();
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->addColumn('orders_count', function ($data) {
                return view(
                    'admin.order.partials.order',
                    compact('data')
                )->render();
            })
            ->addColumn('action', function ($data) {
                $model = 'order';
                return view(
                    'admin.order.partials.data-table-action',
                    compact('data', 'model')
                )->render();
            })
            ->addColumn('status', function ($data) {
                return view(
                    'admin.order.partials.status',
                    compact('data')
                )->render();
            })
            ->addColumn('checkbox', function ($data) {
                return view('admin.common.checkbox', compact('data'))->render();
            })
            ->rawColumns([ 'action', 'orders_count', 'created_at', 'status', 'checkbox', 'payment_gateway', 'delivery_status', 'picked_by', 'full_name', ])
            ->make(true);
    }

}

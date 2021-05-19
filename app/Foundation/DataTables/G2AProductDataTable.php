<?php

namespace Foundation\DataTables;

use App\Foundation\Lib\G2AProductKeys;
use Modules\Payment\Libs\Payment;
use Throwable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class G2aProductDataTable
 * @package Foundation\DataTables
 */
final class G2aProductDataTable
{

    /**
     * Return G2A Product Keys Data table for the given builder
     *
     * @param Builder $builder
     * @return mixed
     * @throws Throwable
     */
    public static function keys($builder)
    {
        return datatables()
            ->of($builder)
            ->addColumn('checkbox', function ($data) {
                return view(
                    'admin.common.checkbox',
                    compact('data')
                )->render();
            })
            ->addColumn('g2a_product', function ($data) {
                return '<a href="' . route('admin.g2a-product.show', $data->g2a_product_id) . '">'. $data->g2a_product_name . '</a>';
            })
            ->addColumn('key', function ($data) {
                return $data->key ?? '<code> Not Imported </code>';
            })
            ->addColumn('original_price', function ($data) {
                return $data->original_price ?? '<code> -- </code>';
            })
            ->addColumn('selling_price', function ($data) {
                return ($data->selling_price ?? "<a href='" . route('admin.g2a-product.key.edit', [$data->g2a_product_id, $data->id]) . "'> Set Now </a>");
            })
            ->addColumn('is_used', function ($data) {
                return view(
                    'admin.gift-cards.partials.is_used',
                    compact('data')
                )->render();
            })
            ->addColumn('transaction', function ($data) {
                return view(
                    'admin.gift-cards.partials.transactions-details',
                    compact('data')
                )->render();
            })
            ->addColumn('buyer', function ($data) {
                if($data->buyer_id){
                    return '<a href="' . route('admin.user.show', $data->buyer_id) . '"> '. $data->buyer_name . '</a>';
                }
                return '<code> Not Applicable </code>';
            })
            ->addColumn('picker', function ($data) {
                if($data->picker_id)
                    return '<a href="' . route('admin.user.show', $data->picker_id) . '"> '. $data->picker_name . '</a>';
                else{
                    if($data->transaction_status == Payment::PAYMENT_STATUS_DELIVERED)
                        return '<code> Automatic </code>';
                    else
                        return '<code> Not Picked </code>';
                }
            })
            ->addColumn('status', function ($data) {
                return '<span class="badge">' . G2AProductKeys::$status[$data->status] . '</span>';
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->addColumn('updated_at', function ($data) {
                return $data->updated_at;
            })
            ->addColumn('actions', function ($data) {
                return view(
                    'admin.g2a.keys.datatable-actions',
                    compact('data')
                )->render();
            })
            ->rawColumns([ 'gift_card', 'key', 'is_used', 'checkbox', 'actions', 'transaction', 'buyer', 'picker', 'status', 'original_price', 'selling_price' ])
            ->make(true);
    }

}

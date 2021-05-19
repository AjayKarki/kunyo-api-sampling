<?php

namespace Foundation\DataTables;

use Modules\Payment\Libs\Payment;
use Throwable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class GiftCardDataTable
 * @package Foundation\DataTables
 */
final class GiftCardDataTable
{

    /**
     * Return Gift Card Codes Data table for the given builder
     *
     * @param Builder $builder
     * @return mixed
     * @throws Throwable
     */
    public static function code($builder)
    {
        return datatables()
            ->of($builder)
            ->addColumn('gift_card', function ($data) {
                return '<a href="' . route('admin.gift-cards.show', $data->gift_cards_id) . '">'. $data->gift_card_name . '</a>';
            })
            ->addColumn('codes', function ($data) {
                return view('admin.gift-cards.codes.partials.code-view', compact('data'));
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
            ->addColumn('created_at', function ($data) {
                return $data->created_at;
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
            ->addColumn('updated_at', function ($data) {
                return $data->updated_at;
            })
            ->addColumn('checkbox', function ($data) {
                return view(
                    'admin.common.checkbox',
                    compact('data')
                )->render();
            })
            ->addColumn('actions', function ($data) {
                return view(
                    'admin.gift-cards.partials.code-actions',
                    compact('data')
                )->render();
            })
            ->rawColumns([ 'gift_card', 'codes', 'is_used', 'checkbox', 'actions', 'transaction', 'buyer', 'picker' ])
            ->make(true);
    }

}

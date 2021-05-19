<?php

namespace Foundation\DataTables;

use Throwable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TopUpDataTable
 * @package Foundation\DataTables
 *
 */
final class TopUpDataTable
{

    /**
     * Return Top Up Amounts Data table for the given builder
     *
     * @param Builder $builder
     * @return mixed
     * @throws Throwable
     */
    public static function amount(Builder $builder)
    {
        return datatables()
            ->of($builder)
            ->addColumn('title', function ($data) {
                return $data->title;
            })
            ->addColumn('price', function ($data) {
                return "<span class='price-value'>{$data->price}</span>" . "<input type='text' class='form-control d-none price-input' name='topup[{$data->id}][price]' value='{$data->price}'>";
            })
            ->addColumn('original_price', function ($data) {
                return"<span class='price-value'>{$data->original_price}</span>" . "<input type='text' class='form-control d-none price-input' name='topup[{$data->id}][original_price]' value='{$data->original_price}'>";
            })
            ->addColumn('status', function ($data) {
                return view(
                    'admin.common.status',
                    compact('data')
                )->render();
            })
            ->addColumn('created_at', function ($data) {
                return $data->created_at;
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
            ->addColumn('int_price', function ($data) {
                $prices = $data->prices;
                $id = $data->id;
                return view('admin.top-up.partials.topup-actions', compact('prices', 'id'));
            })
            ->rawColumns(['title', 'price', 'original_price', 'status', 'checkbox', 'updated_at'])
            ->make(true);
    }

}

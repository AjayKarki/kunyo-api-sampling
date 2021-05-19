<?php

namespace Foundation\Builders\Filters\GiftCardCodes;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 * @package Foundation\Builders\Filters\Category
 */
final class Filter
{

    public static function apply(Builder $builder, array $data)
    {
        $builder = $builder->newQuery();
        $builder = static::filterSearch($builder, Arr::get($data, 'search.value'));
        if ($status = Arr::get($data, 'filter.status')){
            if($status == 1){
                $builder->where('is_used', true);
            } elseif ($status == 2){
                $builder->where('is_used', false);
            }
        }
        else{
            $builder->where('is_used', false);
        }
        if ($status = Arr::get($data, 'filter.include_trashed')){
            $builder->withTrashed();
        }
        return $builder;
    }

    /**
     * Filter on Search box Value
     *
     * @param $query
     * @param $search
     * @return mixed
     */
    public static function filterSearch($query, $search)
    {
        $search = str_replace([' ', '$'], '%', $search);
        if ($search) {
            $query->where(function ($query) use ($search){
                $query->whereHas('giftCard', function ($query) use ($search) {
                    $query->where('name',  'like', '%' . $search . '%');
                });

                $query->orWhere('codes', 'like', '%' . $search . '%');
                $query->orWhere('gift_cards_codes.created_at', 'like', '%' . $search . '%');
                $query->orWhere('buyer.first_name', 'like', '%' . $search . '%')->orWhere('buyer.middle_name', 'like', '%' . $search . '%')->orWhere('buyer.last_name', 'like', '%' . $search . '%');
                $query->orWhere('picker.first_name', 'like', '%' . $search . '%')->orWhere('picker.middle_name', 'like', '%' . $search . '%')->orWhere('picker.last_name', 'like', '%' . $search . '%');
                $query->orWhere('transactions.transaction_id', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }


}

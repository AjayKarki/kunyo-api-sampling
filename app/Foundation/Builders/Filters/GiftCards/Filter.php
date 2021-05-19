<?php

namespace Foundation\Builders\Filters\GiftCards;

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

        if($searchKey = Arr::get($data, 'search.value')){
            $builder->where('gift_cards.name', 'like', '%' . $searchKey . '%');
            $builder->orWhere('gift_cards.slug','like', '%'. $searchKey .'%');
        }

        if ($name = Arr::get($data, 'filter.name')) {
            $builder->where('gift_cards.name', 'like', '%' . $name . '%');
        }

        if ($code = Arr::get($data, 'filter.code')) {
            $builder->whereHas('codes', function ($query) use($code){
                $query->where('gift_cards_codes.codes', 'like', '%' . $code . '%');
            });
        }

        if ($createdFrom = Arr::get($data,'filter.createdAt.from')) {
            $builder->whereDate('gift_cards.created_at', '>=', $createdFrom);
        }

        if ($createdTo = Arr::get($data,'filter.createdAt.to')) {
            $builder->whereDate('gift_cards.created_at', '<=', $createdTo);
        }

        if (!is_null($status = Arr::get($data,'filter.status'))) {
            $builder->where('gift_cards.status', $status);
        }

        if ($type = Arr::get($data, 'filter.type')) {
            $builder->where(function ($query) use ($type){
                if($type == 1)
                    $query->whereColumn('price', 'original_price')->orWhere('original_price', 0);
                elseif($type == 2)
                    $query->whereColumn('price', '!=', 'original_price')->where('original_price', '!=', 0);
            });

        }

        return $builder;
    }

}

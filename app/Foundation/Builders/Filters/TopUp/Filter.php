<?php
namespace Foundation\Builders\Filters\TopUp;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

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
            $builder->where('game_top_ups.name', 'like', '%' . $searchKey . '%');
            $builder->orWhere('game_top_ups.slug','like', '%'. $searchKey .'%');
        }

        if ($name = Arr::get($data, 'filter.name')) {
            $builder->where('game_top_ups.name', 'like', '%' . $name . '%');
        }

        if ($minAmount = Arr::get($data, 'filter.amounts.min')) {
            $builder->whereHas('amounts', function ($query) use($minAmount){
                $query->where('game_top_ups_amounts.price', '>',  $minAmount);
            });
        }

        if ($maxAmount = Arr::get($data, 'filter.amounts.max')) {
            $builder->whereHas('amounts', function ($query) use($maxAmount){
                $query->where('game_top_ups_amounts.price', '<',  $maxAmount);
            });
        }

        if ($createdFrom = Arr::get($data,'filter.createdAt.from')) {
            $builder->whereDate('game_top_ups.created_at', '>=', $createdFrom);
        }

        if ($createdTo = Arr::get($data,'filter.createdAt.to')) {
            $builder->whereDate('game_top_ups.created_at', '<=', $createdTo);
        }

        if (!is_null($status = Arr::get($data,'filter.status'))) {
            $builder->where('game_top_ups.status', $status);
        }

        if ($type = Arr::get($data, 'filter.type')) {
            if($type == 1)
                $builder->whereHas('amounts', function ($query) use($type) {
                    $query->whereColumn('game_top_ups_amounts.price', 'game_top_ups_amounts.original_price')->orWhere('game_top_ups_amounts.original_price', 0);
                });
            elseif($type == 2)
                $builder->whereHas('amounts', function ($query) use($type) {
                    $query->whereRaw('game_top_ups_amounts.price <> game_top_ups_amounts.original_price')->where('game_top_ups_amounts.original_price', '<>', 0);
                });
        }

        return $builder;
    }

}

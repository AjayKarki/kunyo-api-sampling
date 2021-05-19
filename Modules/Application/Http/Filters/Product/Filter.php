<?php

namespace Modules\Application\Http\Filters\Product;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 * @package Modules\Application\Http\Filters\Product
 */
final class Filter
{

    public static function apply(Builder $builder, array $data)
    {
        $builder = $builder->newQuery();

        if ( $keyword = Arr::get($data, 'keyword') ) {
            $builder->whereLike([
                'products.name', 'products.meta_title', 'products.meta_description',
                'products.sale_price',
            ], $keyword);
        }

        if ( $category = Arr::get($data, 'category_id') ) {
            $builder->whereHas('categories', function ($builder) use ($category) {
                $builder->where('id', $category);
            });
        }

        return $builder;
    }

}

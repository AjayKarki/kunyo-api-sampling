<?php

namespace Modules\Application\Http\Filters\Category;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 * @package Modules\Application\Http\Filters\Category
 */
final class Filter
{

    public static function apply(Builder $builder, array $data)
    {
        $builder = $builder->newQuery();

        if ( $keyword = Arr::get($data, 'keyword') ) {
            $builder->whereLike([
                'name',
                'slug',
            ], $keyword);
        }

        return $builder;
    }

}

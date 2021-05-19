<?php

namespace Foundation\Builders\Filters\Collection;

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

        if ($searchKey = Arr::get($data, 'search.value')) {
            $builder->where('collections.name', 'like', '%' . $searchKey . '%');
            $builder->orWhere('collections.description', 'like', '%' . $searchKey . '%');
            $builder->orWhere('collections.status', 'like', '%' . $searchKey . '%');
            $builder->orWhere('collections.created_at', 'like', '%' . $searchKey . '%');
        }

        if ($name = Arr::get($data, 'filter.name')) {
            $builder->where('collections.name', 'like', '%' . $name . '%');
        }


        if ($createdFrom = Arr::get($data, 'filter.createdAt.from')) {
            $builder->whereDate('collections.created_at', '>=', $createdFrom);
        }

        if ($createdTo = Arr::get($data, 'filter.createdAt.to')) {
            $builder->whereDate('collections.created_at', '<=', $createdTo);
        }

        if (!is_null($status = Arr::get($data, 'filter.status'))) {
            $builder->where('collections.status', $status);
        }

        return $builder;
    }

}

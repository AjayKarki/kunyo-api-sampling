<?php

namespace Foundation\Builders\Filters\Package;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 * @package Foundation\Builders\Filters\Package
 */
final class Filter
{
    public static function apply(Builder $builder, array $data){
        $builder = $builder->newQuery();

        if($search = Arr::get($data, 'search.value')) {
            $builder->where('packages.description', 'like', '%' . $search . '%');
            $builder->orWhere('packages.no_of_post', 'like', '%' . $search . '%');
            $builder->orWhere('packages.price', 'like', '%' . $search . '%');
            $builder->orWhere('packages.status', 'like', '%' . $search . '%');
        }

        if($jobId = Arr::get($data, 'filter.jobId')) {
            $builder->where('packages.job_type_id',$jobId );
        }

        if($description = Arr::get($data, 'filter.description')) {
            $builder->where('packages.description', 'like', '%' . $description . '%');
        }
        if ($createdFrom = Arr::get($data,'filter.createdAt.from')) {
            $builder->whereDate('packages.created_at', '>=', $createdFrom);
        }

        if ($createdTo = Arr::get($data,'filter.createdAt.to')) {
            $builder->whereDate('packages.created_at', '<=', $createdTo);
        }
        if(!is_null($status = Arr::get($data, 'filter.status'))) {
            $builder->where('packages.status', $status);
        }
        if ($totalPostStart = Arr::get($data,'filter.totalPost.start')) {
            $builder->where('packages.no_of_post', '>=', $totalPostStart);
        }

        if ($totalPostEnd = Arr::get($data,'filter.totalPost.end')) {
            $builder->where('packages.no_of_post', '<=', $totalPostEnd);
        }
        if ($priceStart = Arr::get($data,'filter.price.start')) {
            $builder->where('packages.price', '>=', $priceStart);
        }

        if ($priceEnd = Arr::get($data,'filter.price.end')) {
            $builder->where('packages.price', '<=', $priceEnd);
        }

        return $builder;

    }
}

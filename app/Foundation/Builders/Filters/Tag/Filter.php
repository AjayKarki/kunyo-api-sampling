<?php
namespace Foundation\Builders\Filters\Tag;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;


/**
 * Class Filter
 * @package Foundation\Builders\Filters\Tag
 */
final class Filter
{
    public static function apply(Builder $builder, array $data){

        $builder = $builder->newQuery();

        if($searchKey = Arr::get($data,'search.value')){
            $builder->where('tag_name', 'like', '%' . $searchKey . '%');
            $builder->orWhere('description', 'like', '%' . $searchKey . '%');
            $builder->orWhere('created_at', 'like', '%' . $searchKey . '%');
            $builder->orWhere('status', 'like', '%' . $searchKey . '%');
        }

        if($tag_name = Arr::get($data, 'filter.title')) {
            $builder->where('tag_name', 'like', '%' . $tag_name . '%');
        }

        if($description = Arr::get($data, 'filter.description')) {
            $builder->where('description', 'like', '%' . $description . '%');
        }

        if ($createdFrom = Arr::get($data,'filter.createdAt.from')) {
            $builder->whereDate('created_at', '>=', $createdFrom);
        }

        if ($createdTo = Arr::get($data,'filter.createdAt.to')) {
            $builder->whereDate('created_at', '<=', $createdTo);
        }

        if(!is_null($status = Arr::get($data, 'filter.status'))) {
            $builder->where('status', $status);
        }
        return $builder;
    }
}

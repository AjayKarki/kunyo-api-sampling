<?php
namespace Foundation\Builders\Filters\Faq;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

/**
 * Class Filter
 * @package Foundation\Builders\Filters\Faq
 */
final class Filter
{
    public static function apply(Builder $builder, array $data)
    {
        $builder = $builder->newQuery();

        if($searchKey = Arr::get($data,'search.value')){
            $builder->where('faq_name', 'like', '%' . $searchKey . '%');
            $builder->orWhere('body', 'like', '%' . $searchKey . '%');
            $builder->orWhere('created_at', 'like', '%' . $searchKey . '%');
            $builder->orWhere('status', 'like', '%' . $searchKey . '%');
        }

        if($title = Arr::get($data, 'filter.title')) {
            $builder->where('faq_name', 'like', '%' . $title . '%');
        }

        if($body = Arr::get($data, 'filter.body')) {
            $builder->where('body', 'like', '%' . $body . '%');
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

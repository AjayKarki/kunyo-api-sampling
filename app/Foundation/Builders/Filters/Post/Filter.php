<?php

namespace Foundation\Builders\Filters\post;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 * @package Foundation\Builders\Filters\post
 */
final class Filter
{

    public static function apply(Builder $builder, array $data) {

        $builder = $builder->newQuery();

        if($value = Arr::get($data, 'search.value')) {
            $builder = static::filterSearch($builder, $value);
        }

        if($title = Arr::get($data, 'filter.title')) {
            $builder->where('posts.title', $title);
        }

        if($content = Arr::get($data, 'filter.content')) {
            $builder->where('posts.content', 'like', '%' . $content . '%');
        }

        if ($createdFrom = Arr::get($data,'filter.createdAt.from')) {
            $builder->whereDate('posts.created_at', '>=', $createdFrom);
        }

        if ($createdTo = Arr::get($data,'filter.createdAt.to')) {
            $builder->whereDate('posts.created_at', '<=', $createdTo);
        }

        if(!is_null($status = Arr::get($data, 'filter.status'))) {
            $builder->where('posts.status', $status);
        }

        if(!is_null($post_type = Arr::get($data, 'filter.post_type'))) {
            $builder->where('posts.post_type', $post_type);
        }

        if($createdBy = Arr::get($data, 'filter.CreatedBy')) {
            $builder->whereHas('user', function ($builder) use ($createdBy)
            {
                $builder->where('first_name', 'like', '%'. $createdBy . '%');
            });
        }
        return $builder;
    }

    /**
     * @param $query
     * @param $search
     * @return mixed
     */
    public static function filterSearch($query, $search){
        $query->whereHas('user', function ($query) use ($search) {
            $query->where('first_name', 'like', '%'. $search . '%');
        });
        $query->orWhere('posts.title', 'like', '%' . $search . '%');
        $query->orWhere('posts.content', 'like', '%' . $search . '%');
        $query->orWhere('posts.views', $search);
        $query->orWhere('posts.status', 'like', '%' . $search . '%');
        return $query;
    }
}

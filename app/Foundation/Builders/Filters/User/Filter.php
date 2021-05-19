<?php

namespace Foundation\Builders\Filters\User;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 * @package Foundation\Builders\Filters\User
 */
final class Filter
{
    public static function apply(Builder $builder, array $data)
    {
        $builder = $builder->newQuery();

        if ($searchKey = Arr::get($data, 'search.value')) {
            $builder = static::filterSearch($builder, $searchKey);
        }

        if ($name = Arr::get($data, 'filter.name')) {
            $builder->whereRaw("REPLACE(CONCAT_WS(' ', COALESCE(users.first_name,''), COALESCE(users.middle_name,''), COALESCE(users.last_name,'')), '  ', ' ') like ?", ['%' . $name . '%']);
        }

        if ($email = Arr::get($data, 'filter.email')) {
            $builder->where('users.email', 'like', '%' . $email . '%');
        }

        if ($user_id = Arr::get($data, 'filter.user_id')) {
            $builder->where('users.id', 'like', '%' . $user_id . '%');
        }

        if ($role = Arr::get($data, 'filter.role')) {
            $builder->whereHas('roles', function ($builder) use ($role) {
                $builder->where('roles.id', $role);
            });
        }

        if ($createdFrom = Arr::get($data, 'filter.creation.start')) {
            $builder->whereDate('users.created_at', '>=', $createdFrom);
        }

        if ($createdTo = Arr::get($data, 'filter.creation.end')) {
            $builder->whereDate('users.created_at', '<=', $createdTo);
        }

        if (Arr::get($data, 'filter.kyc_verification') != null) {
                $builder->where('users.is_verified', Arr::get($data, 'filter.kyc_verification'));
        }

        if ($listed = Arr::get($data, 'filter.is_blacklisted')) {
            $builder->where('users.is_blacklisted', $listed == 2);
        }

        return $builder;
    }

    public static function filterSearch($query, $search)
    {
        return $query->where(function ($query) use ($search){
            $query->where('users.email', 'like', '%' . $search . '%')
            ->orWhereRaw("REPLACE(CONCAT_WS(' ', COALESCE(users.first_name,''), COALESCE(users.middle_name,''), COALESCE(users.last_name,'')), '  ', ' ') like ?", ['%' . $search . '%'])
            ->orWhere('users.phone_number', 'like', '%' . $search . '%');
        });
    }
}

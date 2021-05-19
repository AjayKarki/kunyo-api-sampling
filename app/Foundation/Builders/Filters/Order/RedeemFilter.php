<?php

namespace Foundation\Builders\Filters\Order;

use Foundation\Lib\Role;
use Foundation\Lib\Order;
use Illuminate\Support\Arr;
use Modules\Payment\Libs\Payment;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class RedeemFilter
 * @package Foundation\Builders\Filters\Order
 */
final class RedeemFilter
{

    public static function apply(Builder $builder, array $data)
    {
        $builder = $builder->newQuery();

        if($searchKey = Arr::get($data, 'search.value')) {

            $checker = (int) $searchKey;
            if ($checker > 0) {
                $builder->where(
                    'transactions.transaction_id',
                    'like',
                    '%'.$searchKey.'%'
                );
            } else {

                $searchKey = strtolower($searchKey);
                $builder
                    ->orWhereRaw(
                        "CONCAT_WS(' ', users.first_name, users.middle_name) LIKE ? ", ['%'.$searchKey.'%']);

                $builder
                    ->orWhereRaw(
                        "CONCAT_WS(' ', users.first_name, users.last_name) LIKE ? ", ['%'.$searchKey.'%']);

                $builder
                    ->orWhereRaw(
                        "CONCAT_WS(' ', users.first_name, users.middle_name, users.last_name) LIKE ? ", ['%'.$searchKey.'%']);


            }
        }

        $builder->where('redeem', 1);

        return $builder;
    }

}

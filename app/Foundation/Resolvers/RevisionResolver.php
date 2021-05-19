<?php

namespace Foundation\Resolvers;

use Foundation\Models\TopUp;
use Foundation\Models\TopUpAmount;
use Foundation\Services\RevisionService;

/**
 * Class RevisionResolver
 * @package Foundation\Resolvers
 */
final class RevisionResolver
{

    /* Amount events */

    public static function topUpAmountCreated($topUp, array $data)
    {
//        if ($topUp = TopUp::where('id', $topUp)->first()) {
//            static::revise($topUp, $data);
//        }
    }

    public static function topUpAmountUpdated($topUp, array $data)
    {
//        if ($topUp = TopUp::where('id', $topUp)->first()) {
//            static::revise($topUp, $data);
//        }
    }

    public static function topUpAmountAssigned()
    {

    }

    public static function topUpAmountDeleted()
    {

    }

    /* Gift Card Code events */

    public static function giftCardCodeCreated()
    {

    }

    public static function giftCardCodeUpdated()
    {

    }

    public static function giftCardCodeAssigned()
    {

    }

    public static function giftCardCodeDeleted()
    {

    }

    /**
     * @param $model
     * @param array $args
     * @return mixed
     */
    public static function revise($model, array $args)
    {
        return app(RevisionService::class)->save($model, $args);
    }

}

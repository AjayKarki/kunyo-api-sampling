<?php

namespace Foundation\Handler;

use Neputer\Config\Status;
use Foundation\Models\TopUpAmount;
use Foundation\Services\GiftCardService;

final class ProductModifier
{

    public static function getGiftCard($id)
    {
        return app(GiftCardService::class)
            ->byId($id);
    }

    public static function getTopUp($id)
    {
        return TopUpAmount::query()
            ->select(
                'game_top_ups_amounts.*',
                'top_up.slug as top_up_slug',
                'top_up.name as top_up_name',
                'top_up.image as top_up_image'
            )
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(top_up.metas, '$.discount.imepay')) as imepay_discount")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(top_up.metas, '$.discount.khalti')) as khalti_discount")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(top_up.metas, '$.discount.prabhupay')) as prabhupay_discount")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(top_up.metas, '$.discount.nicasia')) as nicasia_discount")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(top_up.metas, '$.discount.esewa')) as esewa_discount")
            ->leftJoin (
                'game_top_ups as top_up',
                'top_up.id',
                '=',
                'game_top_ups_amounts.game_top_ups_id'
            )
            ->where('game_top_ups_amounts.id', $id)
            ->where('top_up.status', Status::ACTIVE_STATUS)
            ->where('top_up.is_order_disable', Status::INACTIVE_STATUS)
//            ->whereHas('category', function ($query) {
//                return $query->where('categories.status', Status::ACTIVE_STATUS);
//            })
            ->first();
    }

}

<?php

namespace Modules\Application\Http\Controllers\Product;

use Foundation\Lib\Product;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Foundation\Services\TopUpService;
use Foundation\Services\GiftCardService;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\DTOs\Product\GiftCard\GiftCardData;
use Modules\Application\Http\DTOs\Product\TopUp\TopUpData;

/**
 * Class ViewAction
 * @package Modules\Application\Http\Controllers\Product
 */
final class ViewAction extends BaseController
{

    private GiftCardService $giftCardService;

    private TopUpService $topUpService;

    public function __construct(
        GiftCardService $giftCardService,
        TopUpService $topUpService
    )
    {
        $this->giftCardService = $giftCardService;
        $this->topUpService    = $topUpService;
    }

    public function __invoke($productType, $slug) : JsonResponse
    {
        switch ($productType) {
            case Product::PRODUCT_TOP_UP:
                $data = $this->topUpService->query()
                    ->select(
                        'game_top_ups.*',
                        'publishers.name as publisher_name',
                        'developers.name as developer_name',
                        'genres.name as genre_name',
                        'platforms.name as platform_name',
                        'delivery_modes.name as delivery_mode_name',
                        'delivery_times.name as delivery_time_name'
                    )
                    ->selectSub(app('db')
                        ->table('game_top_ups_amounts')
                        ->selectRaw('MAX(game_top_ups_amounts.price) AS max_amount')
                        ->whereColumn('game_top_ups_amounts.game_top_ups_id', 'game_top_ups.id')
                        ->limit(1)
                        ->toSql(), 'max_top_up_amount')
                    ->selectSub(app('db')
                        ->table('game_top_ups_amounts')
                        ->selectRaw('MIN(game_top_ups_amounts.price) AS max_amount')
                        ->whereColumn('game_top_ups_amounts.game_top_ups_id', 'game_top_ups.id')
                        ->limit(1)
                        ->toSql(), 'min_top_up_amount')
                    ->leftJoin('publishers', 'publishers.id', '=', 'game_top_ups.publisher_id')
                    ->leftJoin('developers', 'developers.id', '=', 'game_top_ups.developer_id')
                    ->leftJoin('genres', 'genres.id', '=', 'game_top_ups.genre_id')
                    ->leftJoin('platforms', 'platforms.id', '=', 'game_top_ups.platform_id')
                    ->leftJoin('delivery_modes', 'delivery_modes.id', '=', 'game_top_ups.delivery_mode_id')
                    ->leftJoin('delivery_times', 'delivery_times.id', '=', 'game_top_ups.delivery_time_id')
                    ->with('amounts:price,game_top_ups_id,id,title', 'category:category_name,id,slug', 'attributes:id,title,required,game_top_ups_id,placeholder')
                    ->latest()
                    ->where('game_top_ups.slug', $slug)
                    ->with('amounts:price,game_top_ups_id,id,title', 'category:category_name,id,slug', 'attributes:id,title,required,game_top_ups_id,placeholder')
                    ->first();

                if (is_null($data)) {
                    return $this->responseError(
                        'Not Found',
                        Response::HTTP_NOT_FOUND
                    );
                }
                $productData = TopUpData::fromModel($data);
                break;
            default:
                $data = $this->giftCardService->query()
                    ->select('gift_cards.*',
                        'publishers.name as publisher_name',
                        'developers.name as developer_name',
                        'genres.name as genre_name',
                        'platforms.name as platform_name',
                        'delivery_modes.name as delivery_mode_name',
                        'delivery_times.name as delivery_time_name')
                    ->leftJoin('publishers', 'publishers.id', '=', 'gift_cards.publisher_id')
                    ->leftJoin('developers', 'developers.id', '=', 'gift_cards.developer_id')
                    ->leftJoin('genres', 'genres.id', '=', 'gift_cards.genre_id')
                    ->leftJoin('platforms', 'platforms.id', '=', 'gift_cards.platform_id')
                    ->leftJoin('delivery_modes', 'delivery_modes.id', '=', 'gift_cards.delivery_mode_id')
                    ->leftJoin('delivery_times', 'delivery_times.id', '=', 'gift_cards.delivery_time_id')
                    ->with('category:category_name,id,slug')
                    ->where('gift_cards.slug', $slug)
                    ->first();
                if (is_null($data)) {
                    return $this->responseError(
                        'Not Found',
                        Response::HTTP_NOT_FOUND
                    );
                }
                $productData = GiftCardData::fromModel($data);
        }

        if ($data) {
            return $this->responseOk(
                $productData
            );
        } else {
            return $this->responseError(
                'Not Found',
                Response::HTTP_NOT_FOUND
            );
        }
    }

}

<?php

namespace Modules\Application\Http\Controllers\Product;

use Foundation\Lib\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Services\TopUpService;
use Foundation\Services\GiftCardService;
use Neputer\Supports\DTO\ResponsePaginationData;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\DTOs\Product\TopUp\TopUpCollection;
use Modules\Application\Http\DTOs\Product\GiftCard\GiftCardCollection;

final class RelatedProduct extends BaseController
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

    public function __invoke($productType, $productSlug, Request $request)
    {
        switch ($productType) {
            case Product::PRODUCT_TOP_UP:
                $product = $this->topUpService->query()->where('slug', $productSlug)->first(['id', 'category_id']);

                if ($product) {
                    $query = $this->topUpService->query()
                        ->select('game_top_ups.*')
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
                        ->where('game_top_ups.id', '!=', $product->id)
                        ->where('game_top_ups.category_id', $product->category_id)
                        ->latest();
                    $total = $query->count();
                    $data = $query->simplePaginate(8);
                    $collection = TopUpCollection::fromArray($data->items());
                }

                break;
            default:
                $product = $this->giftCardService->query()->where('slug', $productSlug)->first(['id', 'category_id']);

                if ($product) {
                    $query = $this->giftCardService->query()
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
                        ->where('gift_cards.id', '!=', $product->id)
                        ->where('gift_cards.category_id', $product->category_id)
                        ->latest();
                    $total = $query->count();
                    $data = $query->simplePaginate(8);
                    $collection = GiftCardCollection::fromArray($data->items());
                }
        }

        if (isset($data, $collection, $total)) {
            return new ResponsePaginationData([
                'paginator'  => $data,
                'collection' => $collection,
                'total'      => $total,
                'limit'      => 8
            ]);
        }

        return $this->responseError(
            'Not Found',
            Response::HTTP_NOT_FOUND
        );
    }

}

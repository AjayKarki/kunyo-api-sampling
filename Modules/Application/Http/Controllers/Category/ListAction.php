<?php

namespace Modules\Application\Http\Controllers\Category;

use Foundation\Lib\Product;
use Foundation\Services\GiftCardService;
use Foundation\Services\TopUpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Foundation\Services\CategoryService;
use Foundation\Lib\Category as CategoryType;
use Illuminate\Http\Response;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\DTOs\Category\CategoryCollection;
use Modules\Application\Http\DTOs\Product\GiftCard\GiftCardCollection;
use Modules\Application\Http\DTOs\Product\TopUp\TopUpCollection;
use Neputer\Supports\DTO\ResponsePaginationData;

/**
 * Class ListAction
 * @package Modules\Application\Http\Controllers\Category
 */
final class ListAction extends BaseController
{

    private CategoryService $categoryService;

    /**
     * @var TopUpService
     */
    private TopUpService $topUpService;

    /**
     * @var GiftCardService
     */
    private GiftCardService $giftCardService;

    public function __construct(
        CategoryService $categoryService,
        TopUpService $topUpService,
        GiftCardService $giftCardService
    )
    {
        $this->categoryService = $categoryService;
        $this->topUpService = $topUpService;
        $this->giftCardService = $giftCardService;
    }

    public function __invoke(Request $request, $slug = null)
    {
        $data = [];

        if ($slug) {
            $category = $this->categoryService->bySlug($slug);

            if ($category) {
                $isParent = !$category->parent_id;
                $categoryId = $category->id;

                if ($isParent) {
                    return $this->responseOk([
                        'type' => 'category',
                        'data' => CategoryCollection::fromArray($this->categoryService->children(CategoryType::TYPE_IN_GAME_TOP_UP_CATEGORY, $categoryId)->all())->items(),
                    ]);
                } else {

                    $type = $category->type === Product::PRODUCT_GIFT_CARD_INDEX ? Product::PRODUCT_GIFT_CARD : Product::PRODUCT_TOP_UP;

                    switch ($type) {
                        case Product::PRODUCT_TOP_UP:
                            $query = $this->topUpService->query()
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
                                ->latest()
                                ->where('game_top_ups.category_id', $categoryId)
                                ->with('amounts:price,game_top_ups_id,id,title', 'category:category_name,id,slug', 'attributes:id,title,required,game_top_ups_id,placeholder');

                            $total = $query->count();
                            $data = $query->simplePaginate(20);
                            $collection = TopUpCollection::fromArray($data->items());
                            break;
                        default:

                            $filter = $request->get('sortBy');

                            $query = $this->giftCardService
                                ->query()
                                ->where('category_id', $categoryId)
                                ->when($filter, function ($query) use ($filter) {
                                    if ($filter  === 'high_price') {
                                        $query->orderBy('price', 'DESC');
                                    }

                                    if ($filter  === 'low_price') {
                                        $query->orderBy('price', 'ASC');
                                    }
                                })
                                ->with('category:category_name,id,slug');

                            if (is_null($filter)) {
                                $query = $query->latest();
                            }

                            $total = $query->count();
                            $data = $query->simplePaginate(20);
                            $collection = GiftCardCollection::fromArray($data->items());
                    }

                    return new ResponsePaginationData([
                        'paginator'  => $data,
                        'collection' => $collection,
                        'total'      => $total,
                        'limit'      => 20,
                    ]);
                }
            } else {
                return $this->responseError('404', Response::HTTP_NOT_FOUND);
            }
        }

        return $this->responseOk([
            'type' => 'category',
            'data' => CategoryCollection::fromArray($this->categoryService->only(CategoryType::TYPE_IN_GAME_TOP_UP_CATEGORY)->all())->items(),
        ]);
    }

}

<?php

namespace Modules\Application\Http\Controllers;

use Foundation\Builders\Custom\MergeBuilder;
use Foundation\Services\GiftCardService;
use Foundation\Services\TopUpService;
use Illuminate\Http\Request;
use Modules\Application\Http\Resources\ProductResource;

final class SearchAction extends BaseController
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

    public function __invoke(Request $request)
    {
        $keyword = $request->get('keyword');

        $products = MergeBuilder::apply(
            $this->giftCardService->getLatest(10, $keyword, $request->get('category')),
            $this->topUpService->getLatest(10, $keyword, $request->get('category'))
        );

        if (!$keyword) {
            $products = $products->sortBy('name');
        }

        return $this->responseOk(
            ProductResource::collection($products)
        );
    }

}

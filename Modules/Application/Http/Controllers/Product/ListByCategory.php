<?php

namespace Modules\Application\Http\Controllers\Product;

use Foundation\Lib\Product;
use Illuminate\Http\Request;
use Modules\Application\Http\Services\ProductService;
use Neputer\Supports\DTO\ResponsePaginationData;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\DTOs\Product\TopUp\TopUpCollection;
use Modules\Application\Http\DTOs\Product\GiftCard\GiftCardCollection;

final class ListByCategory extends BaseController
{
    private ProductService $product;

    public function __construct(
        ProductService $product
    )
    {
        $this->product = $product;
    }

    public function __invoke($productType, $categoryId, Request $request): ResponsePaginationData
    {
        switch ($productType) {
            case Product::PRODUCT_TOP_UP:
                $data = $this->product->topUpByCategory($categoryId);
                $collection = TopUpCollection::fromArray($data->items());
                break;
            default:
                $data = $this->product->giftCardByCategory($categoryId);
                $collection = GiftCardCollection::fromArray($data->items());
        }

        return new ResponsePaginationData([
            'paginator'  => $data,
            'collection' => $collection,
        ]);
    }

}

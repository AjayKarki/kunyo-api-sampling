<?php

namespace Modules\Application\Http\Controllers\Product;

use Foundation\Lib\Product;
use Illuminate\Http\Request;
use Neputer\Supports\DTO\ResponsePaginationData;
use Modules\Application\Http\Services\ProductService;
use Modules\Application\Http\Controllers\BaseController;
use Modules\Application\Http\DTOs\Product\TopUp\TopUpCollection;
use Modules\Application\Http\DTOs\Product\GiftCard\GiftCardCollection;

/**
 * Class ListAction
 * @package Modules\Application\Http\Controllers\Product
 */
final class ListAction extends BaseController
{

    private ProductService $product;

    public function __construct(
        ProductService $product
    )
    {
        $this->product = $product;
    }

    public function __invoke($productType, Request $request): ResponsePaginationData
    {
        switch ($productType) {
            case Product::PRODUCT_TOP_UP:
                $query = $this->product->topUps();

                $total = $query->count();
                $data  = $query->simplePaginate(20);
                $collection = TopUpCollection::fromArray($data->items());
                break;
            default:
                $query = $this->product->giftCards();
                $total = $query->count();
                $data  = $query->simplePaginate(20);

                $collection = GiftCardCollection::fromArray($data->items());
        }

        return new ResponsePaginationData([
            'paginator'  => $data,
            'collection' => $collection,
            'total'      => $total,
            'limit'      => 20,
        ]);
    }

}

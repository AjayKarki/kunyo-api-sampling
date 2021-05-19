<?php

namespace App\Http\Controllers\Admin\Actions\Pricing;

use Exception;
use Throwable;
use Foundation\Lib\Product;
use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Foundation\Services\TopUpService;
use Foundation\Services\GiftCardService;
use Foundation\Services\PaymentRegionService;

final class GetPriceAction extends BaseController
{

    private GiftCardService $giftCard;

    private TopUpService $topUp;

    private PaymentRegionService $region;

    public function __construct(
        GiftCardService $giftCard,
        TopUpService $topUp,
        PaymentRegionService $region
    )
    {
        $this->giftCard = $giftCard;
        $this->region   = $region;
        $this->topUp    = $topUp;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            $productType = $request->input('filter.productType') ?? Product::PRODUCT_GIFT_CARD_INDEX;

            $regions = $this->region->pluck(); // @TODO cache

            return datatables()
                ->of(
                    ( $productType == Product::PRODUCT_TOP_UP_INDEX ? $this->topUp : $this->giftCard )
                        ->withPricing($request->only('filter', 'search.value'))
                )
                ->rawColumns([ 'product_detail', ])
                ->setTransformer( function ($item) use ($regions, $productType) {
                    return array_merge([
                        'product_detail' => view('admin.pricing.partials.product-detail',
                            compact('item', 'productType'))->render(),
                    ], GetPriceAction::generateCols($regions, $item));
                } )
                ->toJson();
        }
    }

    public static function generateCols($regions, $item): array
    {
        $cols = [];

        foreach ($regions as $id => $region) {
            $price = $item->prices->firstWhere('country', $id);

            $cols[ str_replace([' ',], '_', strtolower($region)) ?? 'N/A' ] = view('admin.pricing.partials.pricing', compact('price', 'region', 'id', 'item'))->render();

            $cols['last_updated']   = isset($price->updated_at) && !is_null($price->updated_at) ? ($price->updated_at->isToday() ? $price->updated_at->diffForHumans() : $price->updated_at->format('Y-m-d')) : '';
        }

        return $cols;
    }

}

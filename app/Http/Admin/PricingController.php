<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Foundation\Lib\Product;
use Foundation\Models\GiftCard;
use Foundation\Models\TopUp;
use Foundation\Models\TopUpAmount;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Foundation\Models\Pricing;
use Neputer\Supports\BaseController;
use Foundation\Requests\Pricing\{
    StoreRequest,
    UpdateRequest
};
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Foundation\Services\PricingService;

/**
 * Class PricingController
 * @package App\Http\Controllers\Admin
 */
class PricingController extends BaseController
{

    /**
     * The PricingService instance
     *
     * @var $pricingService
     */
    private $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $this->pricingService->createOrUpdate(
            [
                'price' => $request->get('price'),
                'status' => $request->get('status')
            ],
            [
                'country' => $request->get('country'),
                'priceable_type' => $this->resolveModel($request->get('type')),
                'priceable_id' => $request->get('id')
            ]
        );
        return response()->json(['message' => 'Pricing has been saved']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Pricing $pricing
     * @return RedirectResponse
     */
    public function destroy(Pricing $pricing)
    {
        $this->pricingService->delete($pricing);
        flash('success', 'Pricing is deleted successfully !');
        return redirect()->back();
    }

    private function resolveModel($model)
    {
        if ($model == Product::PRODUCT_GIFT_CARD)
            return GiftCard::class;

        elseif ($model == Product::PRODUCT_TOP_UP_AMOUNT)
            return TopUpAmount::class;
    }
}

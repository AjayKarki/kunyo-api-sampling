<?php

namespace App\Http\Controllers\Admin\Actions\Pricing;

use Foundation\Models\GiftCard;
use Illuminate\Http\Request;
use Neputer\Config\Status;
use Neputer\Supports\BaseController;
use Foundation\Services\PricingService;

class UpdatePriceAction extends BaseController
{

    private PricingService $pricing;

    public function __construct( PricingService $pricingService)
    {
        $this->pricing = $pricingService;
    }

    public function __invoke(Request $request)
    {
        if ($request->get('pk')) {
            $this->pricing->query()
                ->where('id', $request->input('pk.id'))
                ->update([
                    'price' => $request->get('value') ?? 0,
                ]);
        } else if ($request->get('regionId') && $request->get('itemId')) {
            $this->pricing->createOrUpdate(
                [
                    'price' => $request->get('value') ?? 0,
                    'status' => Status::ACTIVE_STATUS,
                ],
                [
                    'country' => $request->get('regionId'),
                    'priceable_type' => $request->get('productType'),
                    'priceable_id' => $request->get('itemId'),
                ]
            );
        }
    }

}

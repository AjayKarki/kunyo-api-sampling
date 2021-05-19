<?php

namespace App\Http\Controllers\Admin\Actions\Pricing;

use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Foundation\Services\PaymentRegionService;

class SetPriceAction extends BaseController
{

    private PaymentRegionService $regionService;

    public function __construct( PaymentRegionService $regionService )
    {
        $this->regionService = $regionService;
    }

    public function __invoke(Request $request)
    {
        $data = [];

        $data['regions'] = $this->regionService->pluck();

        return view('admin.pricing.set-price', compact('data'));
    }

}

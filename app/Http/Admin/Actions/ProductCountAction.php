<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Neputer\Supports\BaseController;
use Foundation\Services\OrderService;

class ProductCountAction extends BaseController
{

    /**
     * @var OrderService
     */
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function __invoke(Request $request)
    {
        $data['product-stats'] = collect($this->orderService->getStatsTypeWise($request->get('type')))->toArray();
        return $this->responseOk(
            view('admin.dashboard.product-order', compact('data'))->render()
        );
    }

}

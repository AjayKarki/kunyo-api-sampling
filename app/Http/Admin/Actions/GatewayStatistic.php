<?php

namespace App\Http\Controllers\Admin\Actions;

use Illuminate\Http\Request;
use Modules\Payment\PaymentService;
use Neputer\Supports\BaseController;
use Neputer\Supports\Utility;

class GatewayStatistic extends BaseController
{

    private $payment;

    public function __construct( PaymentService $payment )
    {
        $this->payment = $payment;
    }

    public function __invoke(Request $request)
    {
        return $this->responseOk(
            Utility::resolveGateway($this->payment->sortByUse($request->get('type')))
        );
    }

}

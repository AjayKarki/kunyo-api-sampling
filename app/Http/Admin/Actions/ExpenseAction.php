<?php


namespace App\Http\Controllers\Admin\Actions;


use App\Http\Controllers\Controller;
use Foundation\Services\SettingService;
use Illuminate\Http\Request;

class ExpenseAction extends Controller
{
    /**
     * Add a New Payment Gateway
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function addPaymentGateway(Request $request)
    {
        $this->validate($request, [
            'gateway_name' => 'required',
        ]);

        $gateways = app(SettingService::class)->pluck('expense_payment_gateways');
        $gateways[0][$request->get('gateway_name')] = $request->get('gateway_name');
        app(SettingService::class)->update(['expense_payment_gateways' => $gateways]);
        return response()->json(['msg' => 'Gateway Added']);
    }

    /**
     * Get List of Payment Gateways
     *
     * @return mixed
     */
    public function getPaymentGateway()
    {
        return app(SettingService::class)->pluck('expense_payment_gateways')[0];
    }

}

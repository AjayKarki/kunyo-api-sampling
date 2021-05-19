<?php

namespace App\Http\Controllers\Admin\Actions;

use Foundation\Lib\Order;
use Foundation\Lib\Product;
use Illuminate\Http\Request;
use Modules\Payment\PaymentService;

/**
 * Class UpdateTransactionStatus
 * @package App\Http\Controllers\Admin\Actions
 */
final class UpdateTransactionStatus
{

    private $paymentService;

    public function __construct( PaymentService $paymentService )
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request)
    {
        $response = false;
        $message = 'Cannot update the delivery status for the given order. Order quantity is not already fullfilled.';

        $payment = $this->paymentService->query()->find($request->get('pk'));
        if ($payment) {
            $payment->update([
                'status' =>  $request->get('value'),
            ]);
            $response = true;
            $message = 'You have successfully updated the payment status.';
        }
        return response()
            ->json([
                'success' => $response,
                'message' => $message,
            ]);
    }

}

<?php

namespace App\Http\Controllers\Admin\Actions;

use Modules\Payment\Libs\Payment;
use Modules\Payment\PaymentService;
use Illuminate\Http\RedirectResponse;
use Foundation\Mixins\UpdatePaymentStatus;

/**
 * Class verifyPaymentStatus
 * @package App\Http\Controllers\Admin\Actions
 */
class verifyPaymentStatus
{

    use UpdatePaymentStatus;

    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * verifyPaymentStatus constructor.
     * @param PaymentService $paymentService
     */
    public function __construct( PaymentService $paymentService )
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @param $paymentGatewayId
     * @param $transactionId
     * @return RedirectResponse
     */
    public function __invoke($paymentGatewayId, $transactionId)
    {
        $response = false;
        $type = 'error';
        $message = 'The payment status cannot be checked for the moment.';
        $transaction = $this->paymentService->byTransactionIdentifier($transactionId);

        if (is_null($transaction)) {
            abort(404);
        }

        $response = $this->callRespectiveGateway($transaction, $paymentGatewayId);

        if (is_null($response)) {
            abort(404);
        }

        if ($transaction) {

            if ($response->status) {
                $transaction->update([
                    'status' => Payment::PAYMENT_STATUS_DELIVERED,
                ]);
            }

            if ($response->response) {
                $transaction->update([
                    'metas' => array_merge( (array) $transaction->metas, (array) $response->response),
                ]);
            }

        }

        if ($response->status) {
            $type = 'success';
            $message = 'The payment status is paid for the transaction.';
        }

        flash($type, $message);

        return back();
    }

}

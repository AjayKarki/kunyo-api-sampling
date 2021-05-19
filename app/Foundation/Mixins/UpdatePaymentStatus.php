<?php

namespace Foundation\Mixins;

use Foundation\Lib\PaymentUtil;
use Illuminate\Support\Arr;
use Modules\Payment\Libs\Payment;
use Modules\Payment\Gateway\Khalti\KhaltiHandler;
use Modules\Payment\Gateway\ImePay\ImePayHandler;
use Modules\Payment\Gateway\PrabhuPay\PrabhuPayHandler;

/**
 * Trait UpdatePaymentStatus
 * @package Foundation\Mixins
 */
trait UpdatePaymentStatus
{

    /**
     * Handle the reconfirm/check-status for the transaction according to the given payment gateway
     *
     * @param $transaction
     * @param $paymentGatewayId
     * @return object|null
     */
    public function callRespectiveGateway($transaction, $paymentGatewayId)
    {
        $response = null;

        switch ($paymentGatewayId) {
            case Payment::PAYMENT_GATEWAY_PRABHUPAY:
                $response = $this->prabhuPay($transaction);
                break;
            case Payment::PAYMENT_GATEWAY_IMEPAY:
                $response = $this->imePay($transaction);
                break;
            case Payment::PAYMENT_GATEWAY_KHALTI:
                $response = $this->khalti($transaction);
                break;
            default:
                $response = null;
        }
        return $response;
    }

    /**
     * Return the status for the given transaction paid/unpaid
     *
     * @param $transaction
     * @return object
     */
    public function prabhuPay($transaction)
    {
        if ($transaction) {
            $response = PrabhuPayHandler::checkStatus(
                $transaction->transaction_id
            );

            return (object) [
                'status' => optional($response)->status === "00",
                'response' => $response,
            ];
        }

        return (object) [
            'status' => false,
            'response' => null,
        ];
    }

    /**
     * Return the status for the given transaction paid/unpaid
     *
     * @param $transaction
     * @return object
     */
    public function imePay($transaction)
    {
        if ($transaction) {
            $tokenId = Arr::get((array) $transaction->metas, 'TokenId');
            $refId = Arr::get((array) $transaction->metas, 'RefId');

            if ($tokenId && $refId) {
                $response = ImePayHandler::reconfirm(
                    $refId, $tokenId
                );
                return (object) [
                    'status' => optional($response)->ResponseCode === 0,
                    'response' => $response,
                ];
            }
        }
        return (object) [
            'status' => false,
            'response' => null,
        ];
    }

    /**
     * Return the status for the given transaction paid/unpaid
     *
     * @param $transaction
     * @return object
     */
    public function khalti($transaction)
    {
        if ($transaction) {
            $token = Arr::get((array) $transaction->metas, 'token');
            $amount = Arr::get((array) $transaction->metas, 'amount');

            if (is_null($amount)) {
                $orders = $transaction->load('orders')->orders;

                $amount = PaymentUtil::resolveKhaltiAmount($transaction, $orders);
            }

            if ($token && $amount) {
                $response = KhaltiHandler::reconfirm(
                    $token, $amount
                );

                return (object) [
                    'status' => optional($response)->state === 'Complete',
                    'response' => null,
                ];
            }
        }
        return (object) [
            'status' => false,
            'response' => null,
        ];
    }

}

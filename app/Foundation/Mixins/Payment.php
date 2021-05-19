<?php

namespace Foundation\Mixins;

use Foundation\Lib\PaymentUtil;
use Foundation\Models\KunyoCurrency;
use Foundation\Models\Revision;
use Modules\Payment\Gateway\ImePay\ImePayConfig;
use Modules\Payment\Gateway\ImePay\ImePayHandler;
use Modules\Payment\Gateway\Khalti\KhaltiConfig;
use Modules\Payment\Gateway\PrabhuPay\PrabhuPayHandler;

/**
 * Trait Payment
 * @package Foundation\Mixins
 */
trait Payment
{
    /**
     * @param string $name
     * @return false|int|string
     */
    private function resolvePaymentGateway($name = 'imepay')
    {
        $gateway = 'PAYMENT_GATEWAY_'.strtoupper($name);
        return constant('\Modules\Payment\Libs\Payment::'.$gateway);
    }

    /**
     * Handle IME Pay Payment
     *
     * @param $amount
     * @param $transaction
     * @param $successUrl
     * @param $failUrl
     * @return array
     */
    private function handleImePay($amount, $transaction, $successUrl, $failUrl)
    {
        $response = ImePayHandler::getToken($amount, $transaction->getUuid());
        $transaction->update([
            'metas' => [
                'MerchantCode' => ImePayConfig::getMerchantCode(),
                'TranAmount' => optional($response)->Amount,
                'RefId' => optional($response)->RefId,
                'TokenId' => optional($response)->TokenId,
                'TransactionId' => optional($response)->TransactionId,
                'Msisdn' => optional($response)->Msisdn,
                'ImeTxnStatus' => optional($response)->ImeTxnStatus,
                'RequestDate' => optional($response)->RequestDate,
                'ResponseDate' => optional($response)->ResponseDate,
            ],
        ]);

        return [
            'response' => [
                'MerchantCode' => ImePayConfig::getMerchantCode(),
                'TranAmount' => optional($response)->Amount,
                'RefId' => optional($response)->RefId,
                'TokenId' => optional($response)->TokenId,
                'Source' => 'W',
                'Method' => 'GET',
                'RespUrl' => $successUrl,
                'CancelUrl' => $failUrl,
            ],
            'endpoint' => app()->environment('production') ? 'https://payment.imepay.com.np:7979/WebCheckout/Checkout' : 'https://stg.imepay.com.np:7979/WebCheckout/Checkout',
        ];
    }

    /**
     * Handle Khalti Payment
     *
     * @param $amount
     * @param $transaction
     * @return array
     */
    private function handleKhalti($amount, $transaction)
    {
        return [
            'publicKey' => KhaltiConfig::getPublicKey(),
            'productIdentity' => $transaction->getUuid(),
            'productName' => 'Transaction Order : '.$transaction->getUuid(),
            'productUrl' => route('user.order.show', $transaction->getUuid()),
            'amount'     => $amount * 100,
        ];
    }

    /**
     * Handle Prabhu Pay Payment
     *
     * @param $amount
     * @param $transaction
     * @param $phone
     * @return object
     */
    private function handlePrabhuPay($amount, $transaction, $phone)
    {
        if ($phone) {
            $result = PrabhuPayHandler::start($amount, $transaction->getUuid(), $phone);

            if (isset($result->success) && $result->success) {
                if (isset($result->data->transactionId)) {
                    $transaction->update([
                        'metas' => array_merge( (array) $transaction->metas, [
                            'transactionId' => $result->data->transactionId,
                        ]),
                    ]);
                }
            }
            return $result;
        }
        return (object) ['success' => false, 'message' => 'No Phone Number'];
    }


    /**
     * Verify Prabhu Pay OTP and Confirm Payment
     *
     * @param $transactionId
     * @param $phone
     * @param $otp
     * @param $localTransactionId
     * @return object
     */
    private function verifyOtp($transactionId, $phone, $otp, $localTransactionId)
    {
        $confirmResult = PrabhuPayHandler::confirm($transactionId, $otp, $phone);

        if ($confirmResult) {
            $result = PrabhuPayHandler::checkStatus($localTransactionId);
            if ($result->success) {
                $response = ['success' => true];
            } else {
                $response = ['success' => false, 'message' => optional($result)->message];
            }
            return (object) $response;
        }
    }

    /**
     * Handle Payment Via Bank Deposit
     * @param $transaction
     * @param $bank
     * @param $voucher
     */
    private function handleBankPayment($transaction, $bank, $voucher)
    {
        $voucherPath = $voucher->storeAs('public/images/voucher', md5($voucher . microtime()).'.'.$voucher->extension());

        $transaction->update([
            'metas' => [
                'bank_payment_info' => [
                    'bank_id'        => $bank,
                    'voucher'        => $voucherPath,
                ]
            ]
        ]);
    }
}

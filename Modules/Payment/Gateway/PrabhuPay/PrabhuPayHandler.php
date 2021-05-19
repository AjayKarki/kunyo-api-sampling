<?php

namespace Modules\Payment\Gateway\PrabhuPay;

/**
 * Class PrabhuPayHandler
 * @package Modules\Payment\Gateway\PrabhuPay
 */
final class PrabhuPayHandler
{

    const RESULT_STATUS = [
        '00' => 'Success',
        '98' => 'No Such Transaction Found',
        '97' => 'No Such User Found',
        '96' => 'OTP did not match',
        '99' => 'Pending',
        '95' => 'Unauthorized Access',
        'IN' => 'Transaction initiated',  // Means OTP not verified for the respective transactions
    ];

    /**
     * @param $amount
     * @param $invoiceNo
     * @param $cellphone
     * @param string $remarks
     * @return mixed
     */
    public static function start($amount, $invoiceNo, $cellphone, $remarks = '')
    {
        return json_decode(self::handle('GetOtp', [
            'amount' => $amount,
            'invoiceNo' => $invoiceNo,
            "txnDate" => now()->format('Y-m-d'),
            "merchantId" => PrabhuPayConfig::getMerchantId(),
            "cellPhone" => $cellphone,
            "password" => PrabhuPayConfig::getPassword(),
            "remarks" => $remarks
        ]));
    }

    /**
     * @param $transactionId
     * @param $otp
     * @param $cellphone
     * @return mixed
     */
    public static function confirm($transactionId, $otp, $cellphone)
    {
        return json_decode(self::handle('ConfirmPayment', [
            "merchantId" => PrabhuPayConfig::getMerchantId(),
            "cellPhone" => $cellphone,
            "password" => PrabhuPayConfig::getPassword(),
            "otp" => $otp,
            'transactionId' => $transactionId, // This will be returned after getting otp
        ]));
    }

    /**
     * @param $invoiceNo
     * @return mixed
     */
    public static function checkStatus($invoiceNo)
    {
        return json_decode(self::handle('CheckStatus', [
            "merchantId" => PrabhuPayConfig::getMerchantId(),
            "invoiceNo" => $invoiceNo,
            "password" => PrabhuPayConfig::getPassword(),]));
    }

    /**
     * @param string $key
     * @param $data
     * @return bool|string
     */
    private static function handle(string $key, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, PrabhuPayConfig::getEndpoint().'/'.$key);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}

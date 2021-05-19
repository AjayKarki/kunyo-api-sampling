<?php

namespace Modules\Payment\Gateway\ImePay;

/**
 * Class ImePayHandler
 * @package Modules\Payment\Gateway\ImePay
 */
final class ImePayHandler
{

    const RESULT_STATUS = [
        '0' => 'Payment request Success',
        '1' => 'Payment request failed',
        '2' => 'Error',
        '3' => 'Cancel',
    ];

    public static function confirm($refId, $transactionId, $msIsdn)
    {
        return json_decode(self::handle('Confirm', [
            'TransactionId' => $transactionId,
            "MerchantCode" => ImePayConfig::getMerchantCode(),
            "Msisdn" => $msIsdn,
            "RefId" => $refId,
        ]));
    }

    public static function reconfirm($refId, $tokenId)
    {
        return json_decode(self::handle('Recheck', [
            "MerchantCode" => ImePayConfig::getMerchantCode(),
            "TokenId" => $tokenId,
            "RefId" => $refId,
        ]));
    }

    public static function getToken($amount, $refId)
    {
        return json_decode(self::handle('GetToken', [
            "MerchantCode" => ImePayConfig::getMerchantCode(),
            "Amount" => $amount,
            "RefId" => $refId,
        ]));
    }

    private static function getHeaders($userName, $password, $module)
    {
        return [
            "Authorization: Basic ".base64_encode("{$userName}:{$password}"),
            "Module: " .base64_encode($module),
            "Content-Type: application/json",
        ];
    }

    public static function handle(string $key, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ImePayConfig::getEndpoint().$key);
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::getHeaders(
            ImePayConfig::getApiUser(), ImePayConfig::getPassword(), ImePayConfig::getModule()));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}

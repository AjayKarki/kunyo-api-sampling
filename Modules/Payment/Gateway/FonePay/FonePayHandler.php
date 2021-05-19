<?php

namespace Modules\Payment\Gateway\FonePay;

/**
 * Class FonePayHandler
 *
 * @package Modules\Payment\Gateway\FonePay
 */
final class FonePayHandler
{
 
    /**
     * 0-Success, other than zero are failed transactions
     */
    const RESULT_STATUS = [
        '0' => 'Success',
    ];

    public static function verify($data)
    {
        $dt = date('m/d/Y');
        dd($data);
        return FonePayHandler::handle(
            'merchantRequest/verificationMerchant',
            $data
        );
    }

    public static function generateHash($args = [])
    {
        # code...
    }

    private static function handle(string $key, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, FonePayConfig::getEndpoint().$key . '?' .http_build_query($data));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseXML = curl_exec($ch);

        return simplexml_load_string($responseXML);
    }

}

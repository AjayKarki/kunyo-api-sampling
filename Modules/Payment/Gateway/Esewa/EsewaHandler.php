<?php

namespace Modules\Payment\Gateway\Esewa;

/**
 * Class EsewaHandler
 * @package Modules\Payment\Gateway\Esewa
 */
final class EsewaHandler
{

    /**
     *
     * amt	Amount of product or item or ticket etc
     * txAmt	Tax amount on product or item or ticket etc
     * psc	Service charge by merchant on product or item or ticket etc
     * pdc	Delivery charge by merchant on product or item or ticket etc
     * tAmt	Total payment amount including tax, service and deliver charge. [i.e tAmt = amt + txAmt + psc + tAmt]
     * pid	A unique ID of product or item or ticket etc
     * scd	Merchant code provided by eSewa
     * su	Success URL: a redirect URL of merchant application where customer will be redirected after SUCCESSFUL transaction
     * fu	Failure URL: a redirect URL of merchant application where customer will be redirected after FAILURE or PENDING transaction
     *
     * @param $productData
     */
    public static function action($productData)
    {
        $endpoint = EsewaConfig::getEndpoint() . 'main';

        $data = array_replace_recursive([
            'txAmt' => 0,
            'psc'   => 0,
            'pdc'   => 0,
            'scd'   => EsewaConfig::getMerchant(),
            'su'    => EsewaConfig::getSuccessUrl(route('checkout.result', [
                'gateway' => 'esewa',
                'result'  => 'success',
            ])),
            'fu'    => EsewaConfig::getFailureUrl(route('checkout.result', [
                'gateway' => 'esewa',
                'result'  => 'failure',
            ])),
        ], $productData);

        \Log::info('ESEWA', [
            'formData' => $data,
            'endpoint' => $endpoint,
        ]);
        return [
            'formData' => $data,
            'endpoint' => $endpoint,
        ];
    }

    public static function verify($detail): bool
    {
        $details = array_merge([
            'amt' => null,
            'rid' => null,
            'pid' => null,
            'scd' => EsewaConfig::getMerchant(),
        ], $detail);

        $curl = curl_init(EsewaConfig::getEndpoint() . 'transrec');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $details);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        return str_replace(PHP_EOL, '', strip_tags($response)) == 'Success';
    }


}

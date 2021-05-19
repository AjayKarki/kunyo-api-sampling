<?php

namespace Modules\Payment\Gateway\Khalti;

/**
 * Class KhaltiHandler
 * @package Modules\Payment\Gateway\Khalti
 */
final class KhaltiHandler
{

    public static function reconfirm($token, $amount)
    {
        return json_decode(static::handle('status', [
            'token'  => $token,
            'amount' => $amount,
        ]));
    }

    public static function verify(array $data)
    {
        return json_decode(static::handle(null, $data));
    }

    public static function handle($key, array $data)
    {
        $args = http_build_query($data);

        $endpoint = KhaltiConfig::getEndpoint();

        if ($key) {
            $endpoint = str_replace('verify', $key, $endpoint) . '?'.$args;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);

        if (is_null($key)) { // get request for check status else post
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = ['Authorization: Key '.KhaltiConfig::getSecretKey()];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $response;
    }

}

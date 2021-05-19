<?php

namespace Modules\Sms\Gateway\Sparrow;

/**
 * Class Sms
 * @package Modules\Sms\Gateway\Sparrow
 */
class Sms
{

    /**
     * The Api Endpoint
     *
     * @var string
     */
    private $endpoint = 'http://api.sparrowsms.com/v2/sms/';

    /**
     * Invalid Response
     * If status code is 403
     */
    const INVALID_RESPONSE = [
        1000 => 'A required field is missing',
        1001 => 'Invalid IP Address',
        1002 => 'Invalid Token',
        1003 => 'Account Inactive',
        1004 => 'Account Inactive',
        1005 => 'Account has been expired',
        1006 => 'Account has been expired',
        1007 => 'Invalid Receiver',
        1008 => 'Invalid Sender',
        1010 => 'Text cannot be empty',
        1011 => 'No valid receiver',
        1012 => 'No Credits Available',
        1013 => 'Insufficient Credits',
    ];

    /**
     * @param array $args
     * @return array
     */
    public function send( array $args )
    {
        $args = http_build_query( array_replace_recursive([
            'auth_token' => 0,
            'from'  => 0,
            'to'    => 0,
            'text'  => '',
        ], $args));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            logger((string) $errorMessage);
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status'   => $statusCode,
            'response' => json_decode($response, 1),
        ];
    }

}

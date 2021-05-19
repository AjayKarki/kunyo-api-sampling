<?php

return [

    'imepay' => [
        'is_enabled'   => true,
        'endpoint'      => 'https://stg.imepay.com.np:7979/api/Web/',
        'merchant_code' => 'KUNYO', # Merchant code provided by ImePay
        'apiuser' => 'Kunyo.co', # Api Username provided by ImePay
        'password' => 'ime@12345', # Password provided by ImePay
        'module' => 'KUNYO', # Module provided by ImePay
    ],

    'esewa' => [
        'is_enabled'   => false,
        'endpoint'     => 'https://esewa.com.np/epay/main',
        'merchant_id'  => 'BhwIWQ8SBQEQ',
        'ssl_verifier' => false, // Should be true in production
//        'success_url'  => route('payment.success', 'esewa'),
//        'failure_url'  => route('payment.failure', 'esewa'),
    ],

    'khalti' => [
        'is_enabled'   => true,
        'endpoint'  => 'https://khalti.com/api/v2/payment/verify/',
        'publicKey' => 'test_public_key_1a8d47391a8a45d9bb5d37a56f8688c7',
        'secretKey' => 'test_secret_key_bc62df5bb00c4af6a3f0f1debd939cee',
    ],

    'prabhupay' => [
        'is_enabled'   => true,
        'endpoint'     => 'https://stagesys.prabhupay.com/api/Epayment',
        'merchantId'   => 'Kunyo',
        'password'     => 'kunyo@123',
    ],
    'fonepay'   => [
        'is_enabled'   => true,
        'endpoint'     => 'https://dev-clientapi.fonepay.com/api/merchantRequest/verificationMerchant',
        'pid'          => 'NBQM',
        'md'           => 'P',
        'CRN'          => 'NPR',
        'sectetkey'    => 'a7e3512f5032480a83137793cb2021dc',
    ],

    'nicasia'    => [
        'is_enabled'      => true,
        'merchant_id'     => '100710070000060',
        'endpoint'        => 'https://testsecureacceptance.cybersource.com/pay',
        'verify_endpoint' => 'https://apitest.cybersource.com/', // api.cybersource.com
        'secret_key'      => '2c1c573dff79486e83a10eb5b582f9d4f5e0da8933694ea9906848947ec7759b10922e69ac424e368b8db14d62a712b59fdde187d71844a0b90f87afe02e95b82bf5abf816864be28f2402d178cf582625dd6c6beb9942e5889d527aa498c137e6e794051ed044aa9dad73bb0d0ae35e3506f9ca39384cd6ab054730ee84211f',
        'access_key'      => '96c15918ad783ad2bd003caa948f2ffa',
        'profile_id'      => '87B586FE-4925-47BE-9481-4E9DD4127ED1',
        'currency'        => 'NPR', // For live use 'NPR'; For test use 'USD'
        'payment_method'  => 'card',
        'merchant_key_id'       => 'a570a0e9-a934-4d7b-8262-d8caf0afd40a',
        'merchant_key_secret'   => '3tSULyurIBWio5Z8tnM/kp8P2fabuv8VBE3YH4ALPis=',
    ],

];

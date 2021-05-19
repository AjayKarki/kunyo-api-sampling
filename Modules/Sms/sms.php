<?php

return  [
    'sparrowsms' => [
        'is_enabled'   => true,
        'endpoint' => 'http://api.sparrowsms.com/v2/sms/',
        'token' => 'jlMbFWZEikAOnYSEGGKX',
        'identity' => 'InfoSMS', // from
    ],

    'aakash' => [
        'is_enabled'   => true,
        'endpoint' => 'https://aakashsms.com/admin/public/sms/v3/send',
        'token' => 'ac1acf02e5e2d9956ff11051a04ede7ffaec8a46875c31929544e592f01ca703',
    ],

    // If template is added we need to add patterns for the template in Modules\Sms\Gateway\Sparrow\Template
    'template' => [
        'order_is_created' => 'Order is created. Your Order ID is : {ORDER_ID}. Thank You! KUNYO TEAM',
        'order_is_received' => '{RECEIVER_NAME} Your order ({ORDER_ID}) is delivered. Thank You! KUNYO TEAM',
        'send_otp_code' => 'Kindly use otp {OTP_CODE} for verification. Thank You! KUNYO TEAM',
        'phone_is_verified' => 'Your account for kunyo.co is verified. Please, visit website kunyo.co.',
        'order_is_redeemed' => 'Your request for redeem to order {ORDER_ID} is successfully completed. Thank You! KUNYO TEAM',
    ],
];

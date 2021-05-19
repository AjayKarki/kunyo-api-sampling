<?php

return [

    'email' => [
        'verify' => [
            'email_is_sent' => 'Hello {AUTH_FULL_NAME}, You will receive an Email.
        You need to follow the link provided in the email to verify your account or copy paste the token in the field below.',
            'email_is_failed' => 'Hello {AUTH_FULL_NAME}, you have tried for {TOTAL_TRIED} times . Your email will be blocked after {MAX_TRIES} tries.',
        ],
    ],

    'sms'   => [
        'verify' => [
            'sms_is_sent' => 'Hello {AUTH_FULL_NAME}, otp has been sent to your phone address.',
            'sms_is_failed' => 'Hello {AUTH_FULL_NAME}, you have tried for {TOTAL_TRIED} times . Your phone number will be blocked after {MAX_TRIES} tries.',
        ],
    ],

    'verified' => 'Hello {AUTH_FULL_NAME}, Thank you for verifying.',

];

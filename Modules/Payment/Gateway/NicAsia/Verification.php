<?php

namespace Modules\Payment\Gateway\NicAsia;

final class Verification
{

    /* Sha 256 digest string */
    private $sha256digest = 'SHA-256=';
    private $hmacsha256 = "HmacSHA256";
    private $signature = "Signature:";
    private $postalgoheader = "host date (request-target) digest v-c-merchant-id";
    private $sha256 = "sha256";

    public static function verify($refCode, $transaction)
    {

    }

}

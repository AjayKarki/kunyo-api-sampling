<?php

namespace Foundation\Lib;

/**
 * Class Verify
 * @package Foundation\Lib
 */
final class Verify
{

    const NOTIFIER_EMAIL = 'email';
    const NOTIFIER_PHONE = 'phone';

    const MAX_TRIES = 3; // If reached max tries the sms/email will be disabled for the given account.
    const MAX_TRIES_COOL_DOWN = 1; // Disabled will be enabled after this hour

}

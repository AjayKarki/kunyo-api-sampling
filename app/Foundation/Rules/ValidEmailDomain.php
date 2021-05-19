<?php

namespace App\Rules;

use Foundation\Lib\Meta;
use Illuminate\Contracts\Validation\Rule;

class ValidEmailDomain implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $blockedDomains = json_decode(Meta::get('blocked_email_domains')) ?? [];
        $suppliedDomain = substr($value, strpos($value, "@") + 1);
        return !in_array($suppliedDomain, $blockedDomains);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The supplied email cannot be used for registration! Try a different one.';
    }
}

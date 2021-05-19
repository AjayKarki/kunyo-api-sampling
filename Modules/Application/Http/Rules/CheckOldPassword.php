<?php

namespace Modules\Application\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckOldPassword implements Rule
{

    public function passes($attribute, $value): bool
    {
        return \Hash::check($value, optional(auth()->user())->password);
    }

    public function message(): string
    {
        return 'The :attribute is invalid. You have entered the wrong password.';
    }

}

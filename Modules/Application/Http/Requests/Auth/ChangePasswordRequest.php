<?php

namespace Modules\Application\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Application\Http\Rules\CheckOldPassword;

final class ChangePasswordRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => ['required', new CheckOldPassword],
            'new_password' => 'required|string|min:8|max:25|different:old_password|confirmed', // new_password_confirmation
        ];
    }

}

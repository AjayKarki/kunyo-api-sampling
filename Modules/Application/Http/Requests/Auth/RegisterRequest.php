<?php

namespace Modules\Application\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RegisterRequest
 * @package Modules\Application\Http\Requests\Auth
 */
final class RegisterRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name'   => ['required', 'regex:/^[a-zA-Z]+$/u', 'string', 'min:2', 'max:15'],
            'last_name'    => ['required', 'regex:/^[a-zA-Z]+$/u', 'string', 'min:2', 'max:20'],
            'email'        => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'phone_number' => ['required', 'numeric', 'digits:10', 'unique:users,phone_number'],
            'password'     => ['required', 'string', 'min:8', 'max:25', 'confirmed'],
        ];
    }

}

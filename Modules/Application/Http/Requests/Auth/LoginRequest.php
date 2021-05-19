<?php

namespace Modules\Application\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class LoginRequest
 * @package Modules\Application\Http\Requests\Auth
 */
final class LoginRequest extends FormRequest
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
        if (is_numeric($this->get('username'))) {

            return [
                'username'    => 'required|digits:10',
                'password'    => 'required|string',
            ];

        }
//        elseif (filter_var($this->get('username'), FILTER_VALIDATE_EMAIL)) {
//
//            return [
//                'username'    => 'required|email',
//                'password'    => 'required|string',
//            ];
//
//        }

        return [
            'username'    => 'required|email',
            'password'    => 'required|string',
//            'remember_me' => 'required|bool'
        ];
    }

    public function messages(): array
    {
        return [
            'username.email' => 'Given username should be a valid email address.',
            'username.digits' => 'Given username should be a valid phone address.',
        ];
    }

}

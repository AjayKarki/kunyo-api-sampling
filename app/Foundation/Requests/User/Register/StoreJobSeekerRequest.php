<?php

namespace Foundation\Requests\User\Register;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreJobSeekerRequest
 * @package Foundation\Requests\User\Register
 */
final class StoreJobSeekerRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name'  => 'required|min:2',
            'middle_name' => 'sometimes|nullable|string',
            'last_name'   => 'required|min:2',
            'email'       => 'required|email|unique:users',
            'password'    => [
                'required',
                'string',
                'min:9',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed',
            ],
        ];
    }

}

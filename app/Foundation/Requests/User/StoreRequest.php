<?php

namespace Foundation\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'first_name' => 'required|min:2',
            'middle_name' => 'sometimes|nullable|string',
            'last_name' => 'required|min:2',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|numeric|digits:10|unique:users,phone_number',
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
            'status' => 'required|boolean',
            'info' => 'max: 255',
            'image' => 'image|mimes:jpeg,jpg,png',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(parent::messages(), [
            'roles.required' => 'Please select a role.',
        ]);
    }
}

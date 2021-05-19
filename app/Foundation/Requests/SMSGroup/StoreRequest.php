<?php

namespace Foundation\Requests\SMSGroup;

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
            'name' => 'required',
            'description' => 'required|min:10',
            'status' => 'required|boolean',
            'users' => 'required_unless:added_from,manual|array',
            'manual_users' => 'required_if:added_from,manual|array',
            'manual_users.*.full_name' => 'required',
            'manual_users.*.phone_number' => 'required',
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
            'users.required_unless' => 'Please Select at Least one Phone Number.',
            'manual_users.required_if' => 'Add at least one User',
            'manual_users.*.full_name.required' => 'Provide valid names of all Users',
            'manual_users.*.phone_number.required' => 'Provide valid phone numbers of all Users'
        ]);
    }
}

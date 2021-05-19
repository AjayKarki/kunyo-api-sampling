<?php

namespace Foundation\Requests\User\Register;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreEmployerRequest
 * @package Foundation\Requests\User\Register
 */
final class StoreEmployerRequest extends FormRequest
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
            'last_name'   => 'sometimes|nullable|min:2',
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

            'employer.industry_id' => 'required|integer',
            'employer.primary_contact_type' => 'required',
            'employer.primary_contact_number' => 'required|integer|min:10',
            'employer.contact_address' => 'required',
        ];
    }

    /**
     * Validation Messages
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(parent::messages(), [
            'first_name.required' => 'Please provide the Company Name',
            'first_name.min' => 'Company Name must be at least 2 characters long.',
            'employer.industry_id.required' => 'Please Select the Industry',
            'employer.primary_contact_type.required' => 'Please select contact number type.',
            'employer.primary_contact_number.required' => 'Please enter a numeric contact number.',
            'employer.contact_address.required' => 'Please enter Company Address.',
        ]);
    }

}

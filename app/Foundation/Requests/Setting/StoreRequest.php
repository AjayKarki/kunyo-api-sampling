<?php

namespace Foundation\Requests\Setting;

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
            'apply_online' => 'required|boolean',
            'apply_instruction' => 'required_if:apply_online,1',
            'gender_specific' => 'required|boolean',
            'gender' => 'required_if:gender_specific,1',
            'age_specific' => 'required|boolean',
            'age_comparison' => 'required_if:age_specific,1',
            'age' => 'required_if:age_specific,1',
            'show_organization_details' => 'required|boolean',
            'alternate_organization_name' => 'required_if:show_organization_details,0',
            'alternate_organization_description' => 'required_if:show_organization_details,0',
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
            'apply_instruction.required_if' => 'Provide Apply Instruction if Online Application is not Accepted',
            'gender.required_if' => 'Select gender if this is a Gender specific Job',
            'age_comparison.required_if' => 'Select Age Comparison if this is Age Specific Job',
            'age.required_if' => 'Enter Age if this is Age Specific Job',
            'alternate_organization_name.required_if' => 'Enter Alternate Organization Name if Original is not Shown.',
            'alternate_organization_description.required_if' => 'Enter Alternate Organization Description if Original is not Shown.',
        ]);
    }
}

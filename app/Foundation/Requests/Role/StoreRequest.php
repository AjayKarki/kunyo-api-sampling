<?php

namespace Foundation\Requests\Role;

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
//            'parent_id' => 'required',
            'name' => 'required',
            'slug' => 'required|unique:roles',
            'description' => 'sometimes|nullable|min:20',
            'status' => 'required|boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return parent::messages();
    }
}

<?php

namespace Foundation\Requests\Ticket;

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
            'title' => 'required',
            'description' => 'required|min:10',
            'category' => 'required|integer',
            'priority' => 'required|integer',
            'attachment.*' => 'mimes:jpg,jpeg,png,bmp,svg'
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
            'attachment.*.mimes' => 'All Images should be of JPG, JPEG, PNG, BMP or SVG format.'
        ]);
    }
}

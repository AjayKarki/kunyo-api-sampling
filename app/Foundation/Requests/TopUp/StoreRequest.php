<?php

namespace Foundation\Requests\TopUp;

use Foundation\Models\TopUpAmount;
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
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:200|string|unique:game_top_ups,name',
            'slug' => 'required|max:200|string|unique:game_top_ups,slug',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'sometimes|nullable|min:10',
            'status' => 'required|boolean',

            'amounts.*.title'  => 'sometimes|nullable|max:100', // |unique:game_top_ups_amounts,title
            'amounts.*.price'  => 'sometimes|nullable',
            'amounts.*.original_price'  => 'sometimes|nullable',
            'attributes.*.title'  => 'sometimes|nullable|max:100', // |unique:game_top_ups_attributes,title
            'attributes.*.placeholder'  => 'sometimes|nullable|max:200',
            'attributes.*.required'  => 'sometimes|nullable|boolean',
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
            'amounts.*.title.required' => 'The title field is required.',
            'amounts.*.title.unique' => 'The title field is already taken.',
            'amounts.*.price.required' => 'The price field is required.',
            'amounts.*.title.max' => 'The title field should be less than 100 character.',
            'attributes.*.title.required' => 'The title field is required.',
            'attributes.*.title.unique' => 'The title field is already taken.',
            'attributes.*.title.max' => 'The title field should be less than 100 character.',
            'attributes.*.required.required' => 'The required field is required.',
            'attributes.*.required.boolean' => 'The required field Should either be 0 or 1.',
        ]);
    }
}

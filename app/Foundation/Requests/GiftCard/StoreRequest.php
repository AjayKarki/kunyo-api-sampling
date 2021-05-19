<?php

namespace Foundation\Requests\GiftCard;

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
        return  [
            'category_id'  => 'required|exists:categories,id',
//            'publisher_id' => 'required|exists:publishers,id',
//            'developer_id' => 'sometimes|nullable|exists:developers,id',
//            'genre_id'     => 'sometimes|nullable|exists:genres,id',
//            'platform_id'  => 'sometimes|nullable|exists:platforms,id',
            'name'         => 'required|max:200|string|unique:gift_cards,name',
            'slug'         => 'required|max:200|string|unique:gift_cards,slug',
            'image'        => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'price'        => "required|regex:/^\d+(\.\d{1,2})?$/",
            'original_price'        => "required|regex:/^\d+(\.\d{1,2})?$/",
            'status'       => 'required|boolean',
            'can_purchase'       => 'required|boolean',
            'codes.*.codes'  => 'sometimes|nullable|unique:gift_cards_codes,codes',
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
            'codes.*.codes.required' => 'The code field is required.',
            'codes.*.codes.unique' => 'The code field is already taken.',
        ]);
    }

}

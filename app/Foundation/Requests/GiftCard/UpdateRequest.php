<?php

namespace Foundation\Requests\GiftCard;

class UpdateRequest extends StoreRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'required|max:200|string|unique:gift_cards,name,' .$this->get('id'),
            'slug' => 'required|max:200|string|unique:gift_cards,slug,' .$this->get('id'),
            'codes.*.codes'  => 'sometimes|nullable|unique:gift_cards_codes,codes,' .$this->get('id').',gift_cards_id',
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(parent::messages(), [
                    //
        ]);
    }
}

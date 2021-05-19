<?php

namespace Foundation\Requests\KYC;

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
            'document.front' => 'nullable|mimes:jpg,jpeg,png,svg',
            'document.back' => 'nullable|mimes:jpg,jpeg,png,svg'
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

<?php

namespace Foundation\Requests\User;

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
            'email' => 'required|email|unique:users,email,'.$this->get('id'),
            'phone_number' => 'required|numeric|digits:10|unique:users,phone_number,'.$this->get('id'),
            'password' => 'sometimes|nullable|confirmed',
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
        ]);
    }
}

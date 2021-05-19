<?php

namespace Foundation\Requests\Testimonial;

class UpdateRequest extends StoreRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return array_merge(parent::rules(), [
            'email' => 'required | email | max: 255 |unique:testimonials,email,'.$this->get('id'),
            'photo' => 'sometimes | nullable'
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages() : array
    {
        return array_merge(parent::messages(), [
                    //
        ]);
    }
}

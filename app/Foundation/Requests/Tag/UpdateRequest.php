<?php

namespace Foundation\Requests\Tag;

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
            'tag_name' => 'required|unique:tags,tag_name,'.$this->get('id'),
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

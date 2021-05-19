<?php

namespace Foundation\Requests\TopUp;

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
            'name' => 'required|max:200|string|unique:game_top_ups,name,' .$this->get('id'),
            'slug' => 'required|max:200|string|unique:game_top_ups,slug,' .$this->get('id'),

//            'amounts.*.title'  => 'required|unique:game_top_ups_amounts,title,' .$this->get('id').',game_top_ups_id',
//            'attributes.*.title'  => 'required|unique:game_top_ups_attributes,title,' .$this->get('id').',game_top_ups_id',
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

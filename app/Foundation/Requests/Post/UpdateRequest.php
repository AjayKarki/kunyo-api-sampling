<?php

namespace Foundation\Requests\Post;

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
            'slug' => 'required|max:200|string|unique:posts,slug,' .$this->get('id'),
            'seo_slug' => 'sometimes|nullable|max:150|string|unique:posts,seo_slug,' .$this->get('id'),
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

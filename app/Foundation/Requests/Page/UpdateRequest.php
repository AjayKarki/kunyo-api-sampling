<?php

namespace Foundation\Requests\Page;

class UpdateRequest extends StoreRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required | unique:pages,title,'.$this->get('id'),
            'slug' => 'required',
            'open_in' => 'required',
            'page_type' => 'required',
            'status' => 'required',
            'link'  => 'required_if:page_type,==,link',
            'content'  => 'required_if:page_type,==,content',
            'image' => 'image',
        ];
    }
}

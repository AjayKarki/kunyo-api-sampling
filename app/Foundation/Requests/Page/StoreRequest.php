<?php

namespace Foundation\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $title = $this->title;
        return [
            'title' => 'required | unique:pages,title|max:150',
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

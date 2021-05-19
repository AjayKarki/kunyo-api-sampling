<?php

namespace Modules\Application\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest
 * @package Modules\Application\Http\Requests\Profile
 */
final class UpdateRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if ($this->get('phone_number')) {
            $this->merge([
                'phone' => $this->get('phone_number'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'sometimes|nullable|regex:/^[a-zA-Z]+$/u|string|min:4|max:20',
            'last_name' => 'sometimes|nullable|regex:/^[a-zA-Z]+$/u|string|min:4|max:20',
            'email' => 'sometimes|nullable|string|email|max:50|unique:users,email,'.$this->user()->id,
            'phone_number' => 'sometimes|nullable|numeric|digits:10|unique:users,phone_number,'.$this->user()->id,
            'password' => 'sometimes|nullable|string|min:8|max:25|confirmed',
            'display_picture' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

}

<?php

namespace Foundation\Requests\Testimonial;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{

    protected array $ratingStars;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $range = range(1, 5);
        return [
            'name' => 'required | max: 255',
            'email' => 'required | email | max: 255 |unique:testimonials,email',
            'rating' => 'required|in:' . implode(',', $range),
            'photo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required | max: 255',
            'status' => 'required',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return parent::messages(); // TODO: Change the autogenerated stub
    }
}

<?php

namespace Foundation\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'title' => 'required',
            'type' => 'required',
            'category_id' => 'required',
            'transaction_date' => 'required',
            'amount' => 'required|numeric',
            'payment_method' => 'nullable|string',
            'payee' => 'nullable|string',
            'remarks' => 'nullable|string',
            'receipt' => 'nullable|mimes:jpg,png,jpeg',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return parent::messages(); // TODO: Change the autogenerated stub
    }
}
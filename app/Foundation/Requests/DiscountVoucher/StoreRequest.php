<?php

namespace Foundation\Requests\DiscountVoucher;

use App\Foundation\Lib\DiscountVoucher;
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
            'name' => 'required',
            'voucher' => 'required|unique:discount_vouchers',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'type' => 'required',
            'discount_amount' => 'required_if:type,' . DiscountVoucher::TYPE_AMOUNT . '|nullable|numeric',
            'discount_percent' => 'required_if:type,' . DiscountVoucher::TYPE_PERCENT . '|nullable|numeric|min:0|max:100',
            'max_use' => 'required|numeric',
            'min_order_amount' => 'nullable|numeric',
            'status' => 'required|boolean',
            'use_type' => 'required',
            'user_id' => 'required_if:use_type,'.DiscountVoucher::USE_TYPE_SINGLE_USER,
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(parent::messages(), [
            'discount_amount.required_if' => 'Please Enter Discount Amount',
            'discount_percent.required_if' => 'Please Enter Discount Percent',
            'voucher.unique' => 'Voucher Code is already used',
            'use_type.required' => 'Select type of Usage.',
            'user_id.required_if' => 'Select Customer.'
        ]);
    }
}

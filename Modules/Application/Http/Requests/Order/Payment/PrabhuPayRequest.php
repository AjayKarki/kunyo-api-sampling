<?php

namespace Modules\Application\Http\Requests\Order\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PrabhuPayRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'trans_id_from_prabhu_pay'  => 'required',
            'otp_code'                  => 'required',
            'transaction_id'            => 'required|exists:transactions,transaction_id',
            'phone_number'              => 'required|max:10',
        ];
    }

}

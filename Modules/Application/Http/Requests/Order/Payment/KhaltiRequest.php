<?php

namespace Modules\Application\Http\Requests\Order\Payment;

use Illuminate\Foundation\Http\FormRequest;

class KhaltiRequest extends FormRequest
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
            'transactionId'  => 'required',
            'token'          => 'required',
            'amount'         => 'required',
        ];
    }

}

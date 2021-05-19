<?php

namespace Modules\Application\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Modules\Payment\Libs\Payment;

final class OrderRequest extends FormRequest
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
            'information' => [ 'sometimes', 'nullable', 'max:200', ],
        ];
    }

    public function withValidator(Validator $validator)
    {
        if ($this->get('payment_gateway') == Payment::PAYMENT_GATEWAY_PRABHUPAY) {
            $validator->addRules([
                'phone_number' => [ 'required', 'max:10', ]
            ]);
        }
    }

}

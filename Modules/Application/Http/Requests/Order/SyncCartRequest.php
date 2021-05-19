<?php

namespace Modules\Application\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SyncCartRequest
 * @package Modules\Application\Http\Requests\Order
 */
final class SyncCartRequest extends FormRequest
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
            'cart.*.product_id'  => 'required|exists:products,id',
            'cart.*.quantity'    => 'required',
        ];
    }

}

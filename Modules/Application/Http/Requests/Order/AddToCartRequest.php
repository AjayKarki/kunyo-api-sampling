<?php

namespace Modules\Application\Http\Requests\Order;

use Foundation\Lib\Product;
use Foundation\Models\TopUp;
use Foundation\Models\GiftCard;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AddToCartRequest
 * @package Modules\Application\Http\Requests\Order
 */
final class AddToCartRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge(
            [
                'entity' => $this->get('type') == Product::PRODUCT_TOP_UP_INDEX ? 'game_top_ups_amounts' : 'gift_cards',
            ]
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $table = $this->get('product_type') == Product::PRODUCT_TOP_UP_INDEX ? 'game_top_ups_amounts' : 'gift_cards';

        return [
            'product_id'  => 'required|exists:'. $table. ',id',
            'type'        => 'required|in:'.Product::PRODUCT_TOP_UP_INDEX.','.Product::PRODUCT_GIFT_CARD_INDEX,
            'quantity'    => 'required|integer',
        ];
    }

}

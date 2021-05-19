<?php

namespace Modules\Application\Http\Requests\Seller;

use Modules\ClassifiedAd\Lib\Attribute;
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
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|max:200|string',
            'description' => 'sometimes|nullable|min:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images' => 'max:3',
            'price'            => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'is_price_negotiable' => 'required|boolean',
            'is_delivery_available' => 'required|boolean',
            'delivery_range' => 'required|in:' .implode(',', array_keys(Attribute::deliveryRange())),
//            'delivery_price',
            'warranty_type' => 'required|boolean',
            'warranty_type_period' => 'sometimes|nullable|max:60',
            'product_condition' => 'required|in:' .implode(',', array_keys(Attribute::conditionOfProduct())),
//            'seller_status'  => 'required|in:' .implode(',', array_keys(Attribute::sellerStatus())),
//            'is_approved' => 'required|in:' .implode(',', Attribute::adminStatus()),
            'start_date'  =>  'required|date_format:Y-m-d|after:yesterday',
            'run_ad_till' => 'required|in:' .implode(',', array_keys(Attribute::runAdsTill())),
            'aggreable' => 'required',
//            'end_date'    =>  'required|date|after_or_equal:start_date',
        ];
    }

    public function messages() {
        return [
            'images.*.max' => 'Image size should be less than 2mb',
            'images.*.mimes' => 'Only jpeg, png, bmp,tiff files are allowed.',
            'images.max' => 'You cannot upload more than 3 images.'
        ];
    }

}

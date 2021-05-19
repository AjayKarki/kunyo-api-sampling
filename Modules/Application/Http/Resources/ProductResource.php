<?php

namespace Modules\Application\Http\Resources;

use Foundation\Lib\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $type = $this->getTable() === 'gift_cards' ? Product::PRODUCT_GIFT_CARD : Product::PRODUCT_TOP_UP;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'min_top_up_amount' => $this->min_top_up_amount,
            'max_top_up_amount' => $this->max_top_up_amount,
            'type' => $type,
            'image' => get_image_url($type, $this->image),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

}

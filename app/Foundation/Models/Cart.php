<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel;

final class Cart extends BaseModel
{

    protected $fillable = [
        'identifier', 'product_id', 'price', 'quantity', 'metas', 'product_type', 'cart_type', 'added_by',
    ];

    protected $casts = [
        'metas' => 'array',
    ];

    public function addedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

}

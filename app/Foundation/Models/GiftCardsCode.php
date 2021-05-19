<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class GiftCardsCode
 * @package Foundation\Models
 */
class GiftCardsCode extends Model
{
    use SoftDeletes;

    protected $table = 'gift_cards_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gift_cards_id', 'codes', 'is_used', 'deleted_by'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($code) {
            $code->deleted_by = auth()->id();
            $code->save();
        });
    }

    /**
     * Get the code.
     *
     * @param  string  $value
     * @return string
     */
    public function getCodesAttribute($value)
    {
        if (optional(auth()->user())->bypass_code_view)
            return $value;
        return substr($value, 0, 3) . str_repeat("*", strlen($value));
    }

    /**
     * Check if a Code is Used
     *
     * @return bool
     */
    public function isUsed()
    {
        return $this->is_used == true;
    }

    /**
     * Get the owning gift card
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class, 'gift_cards_id', 'id');
    }

    public function history()
    {
        return $this->morphMany(History::class, 'historyable');
    }

}

<?php

namespace Foundation\Models;

use Modules\Payment\Payment;
use Neputer\Supports\BaseModel as Model;

/**
 * Class TransactionConversation
 * @package Foundation\Models
 */
class TransactionConversation extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message', 'author_id', 'transaction_id', 'acknowledged', 'acknowledged_by_admin',
    ];

    public function transaction()
    {
        return $this->belongsTo(Payment::class, 'transaction_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}

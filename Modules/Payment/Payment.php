<?php

namespace Modules\Payment;

use Foundation\Models\Order;
use Foundation\Models\TransactionConversation;
use Foundation\Models\User;
use Neputer\Supports\BaseModel;
use Neputer\Supports\Mixins\usesUuid;

/**
 * Class Payment
 * @package Modules\Payment
 */
final class Payment extends BaseModel
{

    use usesUuid;

    protected $table = 'transactions';

    protected $fillable = [
        'transaction_id', 'reference_id', 'payment_gateway_id',
        'status',  'extra_information', 'is_delivered', 'metas',
        'user_id', 'picked_by', 'service_charge', 'is_notified', 'redeem',
        'recheck_status', 'rechecked_date', 'recheck_asked_date', 'recheck_message',
        'voucher_discount', 'discount_voucher_id', 'discount_voucher_used', 'discount_voucher_code', 'product_names',
    ];

    protected $casts = [
        'metas' => 'array',
    ];

    protected $dates = [
        'rechecked_date', 'recheck_asked_date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'transaction_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function picker()
    {
        return $this->belongsTo(User::class, 'picked_by', 'id');
    }

    public function conversations()
    {
        return $this->hasMany(TransactionConversation::class, 'transaction_id');
    }

    public function unreadConversations()
    {
        return $this->hasMany(TransactionConversation::class, 'transaction_id')->where('acknowledged', false);
    }

    public function unreadConversationsByAdmin()
    {
        return $this->hasMany(TransactionConversation::class, 'transaction_id')->where('acknowledged_by_admin', false);
    }

    public function getUuidKey()
    {
        return 'transaction_id';
    }
}

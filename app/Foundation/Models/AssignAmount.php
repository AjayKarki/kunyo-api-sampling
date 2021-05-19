<?php

namespace Foundation\Models;

use Neputer\Supports\BaseModel as Model;

/**
 * Class AssignAmount
 * @package Foundation\Models
 */
class AssignAmount extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'debit',
        'credit',
        'user_id',
        'type',
        'assigned_by',
        'order_id',
        'order_type',
        'created_at',
    ];

    /**
     * User to whom the Amount is assigned
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Order for debit amount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Admin who assigned the amount
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

}

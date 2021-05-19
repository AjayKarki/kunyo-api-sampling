<?php

namespace Foundation\Models;

use Foundation\Lib\Ticket as TicketLib;
use Neputer\Supports\BaseModel as Model;

/**
 * Class Ticket
 * @package Foundation\Models
 */
class Ticket extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'category', 'status', 'priority', 'attachment', 'order_id', 'created_by', 'assigned_to'
    ];

    /**
     * Check whether a ticket is open
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->status != TicketLib::STATUS_CLOSED;
    }

    /**
     * Check if the Ticket is resolved
     *
     * @return bool
     */
    public function isResolved()
    {
        return $this->status == TicketLib::STATUS_RESOLVED;
    }

    /**
     * Check if the issue is assigned to an admin
     *
     * @return bool
     */
    public function isAssigned()
    {
        return $this->assigned_to != null;
    }


    /**
     * The events related to the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(TicketEvent::class);
    }

    /**
     * The User who created the Ticked
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The Admin/Mod to whom the Ticked is assigned to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * The reviews of the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(TicketReview::class);
    }

    /**
     * Get this Ticket Images
     */
    public function images()
    {
        return $this->morphMany('Foundation\Models\Image', 'imageable');
    }

}

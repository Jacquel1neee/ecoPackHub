<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnlinkRequest extends Model
{
    protected $fillable = [
        'requester_id',
        'target_id',
        'status',
        'message',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get the user who requested to unlink (requester)
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Get the user who is being requested to unlink from (target / upline)
     */
    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_id');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ][$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'accepted' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
        ][$this->status] ?? 'secondary';
    }
}
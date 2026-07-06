<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoteRequest extends Model
{
    protected $fillable = [
        'promoter_id',
        'target_id',
        'status',
        'message',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get the user who sent the promote request (promoter)
     */
    public function promoter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'promoter_id');
    }

    /**
     * Get the user who is being promoted (target)
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
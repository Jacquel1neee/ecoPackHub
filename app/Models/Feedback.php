<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id', 'name', 'email', 'subject', 'message',
        'status', 'is_read', 'last_reply_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'last_reply_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FeedbackReply::class)->orderBy('created_at', 'asc');
    }

    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Pending',
            'replied' => 'Replied',
            'closed' => 'Closed',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'replied' => 'info',
            'closed' => 'success',
        ][$this->status] ?? 'secondary';
    }
}
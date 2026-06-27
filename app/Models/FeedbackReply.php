<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackReply extends Model
{
    protected $table = 'feedback_replies';

    protected $fillable = [
        'feedback_id', 'admin_id', 'reply_message',
        'sender_type', 'sender_name', 'is_read_by_admin', 'is_read_by_user'
    ];

    protected $casts = [
        'is_read_by_admin' => 'boolean',
        'is_read_by_user' => 'boolean',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isFromUser()
    {
        return $this->sender_type === 'user';
    }

    public function isFromAdmin()
    {
        return $this->sender_type === 'admin';
    }
}
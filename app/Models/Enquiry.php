<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enquiry extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'product_code', 'product_name',
        'company_name', 'contact_person', 'phone', 'email',
        'quantity', 'message', 'status', 'admin_notes', 'is_read', 'last_reply_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'last_reply_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(EnquiryReply::class)->orderBy('created_at', 'asc');
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

    public function getHasUnreadUserRepliesAttribute()
    {
        if (!$this->relationLoaded('replies')) {
            $this->load('replies');
        }
        return $this->replies->where('sender_type', 'admin')->where('is_read_by_user', false)->count() > 0;
    }

    public function getHasUnreadAdminRepliesAttribute()
    {
        if (!$this->relationLoaded('replies')) {
            $this->load('replies');
        }
        return $this->replies->where('sender_type', 'user')->where('is_read_by_admin', false)->count() > 0;
    }
}
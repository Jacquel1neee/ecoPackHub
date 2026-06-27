<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnquiryReply extends Model
{
    protected $fillable = [
        'enquiry_id', 'admin_id', 'reply_message', 
        'sender_type', 'sender_name', 'is_read_by_user', 'is_read_by_admin'
    ];

    protected $casts = [
        'is_read_by_user' => 'boolean',
        'is_read_by_admin' => 'boolean',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
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

    public function getSenderNameAttribute()
    {
        if ($this->sender_type === 'user') {
            return $this->sender_name ?? $this->enquiry->contact_person ?? 'Customer';
        }
        return $this->sender_name ?? 'Admin';
    }
}
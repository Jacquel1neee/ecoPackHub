<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderItem;

class Order extends Model
{
    protected $fillable = [
        'user_id', 
        'order_number', 
        'total_amount', 
        'status', 
        'payment_status',        // 添加这一行
        'delivery_method',       // Delivery method: shipping or self pickup
        'shipping_address', 
        'phone', 
        'notes'
    ];

    /**
     * Delivery method constants
     */
    const DELIVERY_SHIPPING = 'shipping';
    const DELIVERY_SELFPICKUP = 'selfpickup';

    /**
     * Payment status constants
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    /**
     * Get delivery method label with icon
     */
    public function getDeliveryMethodLabelAttribute()
    {
        return [
            'shipping' => '🚚 Shipping',
            'selfpickup' => '🏪 Self Pickup',
        ][$this->delivery_method] ?? $this->delivery_method;
    }

    /**
     * Get delivery method badge color
     */
    public function getDeliveryMethodColorAttribute()
    {
        return [
            'shipping' => 'primary',
            'selfpickup' => 'success',
        ][$this->delivery_method] ?? 'secondary';
    }

    /**
     * Get delivery method icon class
     */
    public function getDeliveryMethodIconAttribute()
    {
        return [
            'shipping' => 'fa-truck',
            'selfpickup' => 'fa-store',
        ][$this->delivery_method] ?? 'fa-box';
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute()
    {
        return [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
        ][$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Get payment status color
     */
    public function getPaymentStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
        ][$this->payment_status] ?? 'secondary';
    }

    /**
     * Get the user who placed this order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in this order
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get formatted status label
     */
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ][$this->status] ?? $this->status;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
        ][$this->status] ?? 'secondary';
    }
}
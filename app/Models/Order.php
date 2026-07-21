<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderItem;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id', 
        'order_number', 
        'subtotal',          // Subtotal before shipping
        'shipping_fee',      // Shipping fee amount
        'total_amount',      // Grand total (subtotal + shipping_fee)
        'status',
        'payment_status',
        'delivery_method',   // shipping or selfpickup
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
     * Get formatted shipping fee with currency
     */
    public function getShippingFeeFormattedAttribute()
    {
        if ($this->shipping_fee > 0) {
            return 'RM ' . number_format($this->shipping_fee, 2);
        }
        return 'FREE';
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
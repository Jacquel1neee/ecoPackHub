<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'vendor_id', 'packing_quantity_option_id', 'size', 'packing_quantity',
        'price', 'vendor_price', 'vendor_quantity', 'stock',
        'discount_price', 'discount_percentage', 'is_discount_active'
    ];

    protected $casts = [
        'discount_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_discount_active' => 'boolean',
        'vendor_quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function packingQuantityOption(): BelongsTo
    {
        return $this->belongsTo(PackingQuantityOption::class, 'packing_quantity_option_id');
    }

    public function getPackingQuantityDisplayAttribute(): string
    {
        return $this->packingQuantityOption?->name ?? $this->packing_quantity ?? '';
    }

    public function getHasActiveDiscountAttribute(): bool
    {
        return (bool) $this->is_discount_active
            && ($this->discount_price !== null || $this->discount_percentage !== null);
    }

    public function calculateDiscountedPrice(float $basePrice): float
    {
        if (! $this->has_active_discount) {
            return $basePrice;
        }

        if ($this->discount_price !== null) {
            $discounted = (float) $this->discount_price;
            return max(0, min($basePrice, $discounted));
        }

        if ($this->discount_percentage !== null) {
            $percentage = max(0, min(100, (float) $this->discount_percentage));
            return round($basePrice * (1 - ($percentage / 100)), 2);
        }

        return $basePrice;
    }

    public function getDiscountedPriceAttribute(): float
    {
        return $this->calculateDiscountedPrice((float) $this->price);
    }
}
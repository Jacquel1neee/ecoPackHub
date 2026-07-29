<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'vendor_id', 'packing_quantity_option_id', 'size', 'packing_quantity', 'price', 'vendor_price', 'vendor_quantity', 'stock'
    ];

    protected $casts = [
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
}
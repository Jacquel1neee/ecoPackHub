<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'code', 'name', 'description',
        'packing_quantity', 'material', 'size', 'image', 'image_path', 'price', 'stock'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalSalesAttribute(): int
    {
        return $this->sales()->sum('quantity_sold');
    }

    public function getTotalRevenueAttribute(): float
    {
        return $this->sales()->sum('total_revenue');
    }
}
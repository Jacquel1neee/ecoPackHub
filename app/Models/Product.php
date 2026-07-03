<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'code', 'name', 'description', 
        'material', 'image', 'image_path', 'image_path2', 
        'image_path3', 'image_path4', 'image_path5', 
        'image_path6', 'image_path7', 'product_group'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getMinPriceAttribute()
    {
        return $this->variants->min('price') ?? 0;
    }

    public function getMaxPriceAttribute()
    {
        return $this->variants->max('price') ?? 0;
    }

    public function getTotalStockAttribute()
    {
        return $this->variants->sum('stock');
    }

    public function getSizesAttribute()
    {
        return $this->variants->pluck('size')->filter()->unique()->values();
    }

    public function getTotalSalesAttribute(): int
    {
        return $this->sales()->sum('quantity_sold') ?? 0;
    }

    public function getTotalRevenueAttribute(): float
    {
        return $this->sales()->sum('total_revenue') ?? 0;
    }
}
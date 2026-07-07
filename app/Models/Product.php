<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public function resolveImageUrl(?string $value): string
    {
        if (!$value) {
            return '';
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            $placeholderMatch = null;
            if (preg_match('/text=([^&]+)/i', $value, $matches)) {
                $placeholderMatch = $matches[1];
            }

            if ($placeholderMatch) {
                $normalizedName = str_replace([' ', '-'], ['_', '_'], $placeholderMatch);
                $normalizedName = strtoupper($normalizedName);
                $candidateNames = array_unique([
                    $placeholderMatch,
                    $normalizedName,
                    $normalizedName . '.png',
                    str_replace(' ', '_', $placeholderMatch),
                    str_replace('-', '_', $placeholderMatch),
                    str_replace([' ', '-'], ['_', '_'], $placeholderMatch),
                ]);

                foreach ($candidateNames as $candidateName) {
                    $candidatePath = dirname(__DIR__, 2) . '/public/images/' . $candidateName . '.png';
                    if (file_exists($candidatePath)) {
                        return '/images/' . $candidateName . '.png';
                    }
                }
            }

            return $value;
        }

        if (Str::startsWith($value, '/')) {
            return $value;
        }

        if (Str::endsWith($value, ['.png', '.jpg', '.jpeg', '.gif', '.webp'])) {
            $filename = basename($value);
            $candidatePath = dirname(__DIR__, 2) . '/public/images/' . $filename;
            if (file_exists($candidatePath)) {
                return '/images/' . $filename;
            }
        }

        if (Str::startsWith($value, 'images/')) {
            return '/' . $value;
        }

        if (Str::contains($value, '/images/')) {
            return '/' . ltrim($value, '/');
        }

        if (Str::contains($value, 'public/')) {
            return '/' . Str::replaceFirst('public/', '', $value);
        }

        return asset($value);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->resolveImageUrl($this->image_path ?: $this->image);
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
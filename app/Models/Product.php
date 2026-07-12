<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'code', 'name', 'description', 
        'material', 'image', 'image_path', 'image_path2', 
        'image_path3', 'image_path4', 'image_path5', 
        'image_path6', 'image_path7', 'product_group'
    ];

    /**
     * Category relationship
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Variants relationship
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Sales relationship
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Vendors relationship (many-to-many with pivot)
     */
    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'product_vendor')
                    ->withPivot('price', 'is_preferred')
                    ->withTimestamps();
    }

    /**
     * Get the preferred vendor for this product
     */
    public function getPreferredVendorAttribute()
    {
        return $this->vendors()->wherePivot('is_preferred', true)->first();
    }

    /**
     * Get vendor prices as array
     */
    public function getVendorPricesAttribute(): array
    {
        return $this->vendors->pluck('pivot.price', 'name')->toArray();
    }

    /**
     * Get vendor price list formatted
     */
    public function getVendorPriceListAttribute(): string
    {
        $list = [];
        foreach ($this->vendors as $vendor) {
            $price = number_format($vendor->pivot->price, 2);
            $preferred = $vendor->pivot->is_preferred ? ' ★' : '';
            $list[] = $vendor->name . ': RM ' . $price . $preferred;
        }
        return implode(' | ', $list);
    }

    /**
     * Get the minimum vendor price
     */
    public function getMinVendorPriceAttribute()
    {
        return $this->vendors->min('pivot.price');
    }

    /**
     * Get the maximum vendor price
     */
    public function getMaxVendorPriceAttribute()
    {
        return $this->vendors->max('pivot.price');
    }

    // ===== Image Handling (Existing Methods) =====

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
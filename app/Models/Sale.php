<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = ['product_id', 'quantity_sold', 'total_revenue', 'sale_date'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
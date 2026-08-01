<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (! Schema::hasColumn('product_variants', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            }

            if (! Schema::hasColumn('product_variants', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_price');
            }

            if (! Schema::hasColumn('product_variants', 'is_discount_active')) {
                $table->boolean('is_discount_active')->default(false)->after('discount_percentage');
            }
        });

        if (Schema::hasTable('products')) {
            $products = DB::table('products')
                ->select('id', 'discount_price', 'discount_percentage', 'is_discount_active')
                ->where(function ($query) {
                    $query->whereNotNull('discount_price')
                        ->orWhereNotNull('discount_percentage')
                        ->orWhere('is_discount_active', true);
                })
                ->get();

            foreach ($products as $product) {
                DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->update([
                        'discount_price' => $product->discount_price,
                        'discount_percentage' => $product->discount_percentage,
                        'is_discount_active' => (bool) $product->is_discount_active
                            && ($product->discount_price !== null || $product->discount_percentage !== null),
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $drop = [];
            if (Schema::hasColumn('product_variants', 'is_discount_active')) {
                $drop[] = 'is_discount_active';
            }
            if (Schema::hasColumn('product_variants', 'discount_percentage')) {
                $drop[] = 'discount_percentage';
            }
            if (Schema::hasColumn('product_variants', 'discount_price')) {
                $drop[] = 'discount_price';
            }

            if (! empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};

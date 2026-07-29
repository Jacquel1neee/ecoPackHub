<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('product_vendor')) {
            return;
        }

        $variants = DB::table('product_variants')
            ->select('product_id', 'vendor_id', 'vendor_price')
            ->whereNotNull('vendor_id')
            ->orderBy('product_id')
            ->orderBy('id')
            ->get();

        $preferredVendorByProduct = [];

        foreach ($variants as $variant) {
            if (! isset($preferredVendorByProduct[$variant->product_id])) {
                $preferredVendorByProduct[$variant->product_id] = $variant->vendor_id;
            }

            $price = $variant->vendor_price ?? 0;

            $existing = DB::table('product_vendor')
                ->where('product_id', $variant->product_id)
                ->where('vendor_id', $variant->vendor_id)
                ->first();

            if ($existing) {
                DB::table('product_vendor')
                    ->where('id', $existing->id)
                    ->update([
                        'price' => $price,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('product_vendor')->insert([
                    'product_id' => $variant->product_id,
                    'vendor_id' => $variant->vendor_id,
                    'price' => $price,
                    'is_preferred' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        foreach ($preferredVendorByProduct as $productId => $vendorId) {
            DB::table('product_vendor')
                ->where('product_id', $productId)
                ->update([
                    'is_preferred' => false,
                    'updated_at' => now(),
                ]);

            DB::table('product_vendor')
                ->where('product_id', $productId)
                ->where('vendor_id', $vendorId)
                ->update([
                    'is_preferred' => true,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // no-op
    }
};
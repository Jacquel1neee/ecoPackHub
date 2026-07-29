<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_variants', 'packing_quantity_option_id')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->foreignId('packing_quantity_option_id')
                    ->nullable()
                    ->after('size')
                    ->constrained('packing_quantity_options')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('product_variants') && Schema::hasTable('packing_quantity_options')) {
            $distinctPackingQuantities = DB::table('product_variants')
                ->whereNotNull('packing_quantity')
                ->distinct()
                ->pluck('packing_quantity');

            foreach ($distinctPackingQuantities as $packingQuantity) {
                DB::table('packing_quantity_options')->updateOrInsert(
                    ['name' => $packingQuantity],
                    ['is_active' => true, 'updated_at' => now(), 'created_at' => now()]
                );
            }

            $variants = DB::table('product_variants')
                ->whereNull('packing_quantity_option_id')
                ->orderBy('id')
                ->get(['id', 'packing_quantity']);

            foreach ($variants as $variant) {
                if (! $variant->packing_quantity) {
                    continue;
                }

                $optionId = DB::table('packing_quantity_options')
                    ->where('name', $variant->packing_quantity)
                    ->value('id');

                if ($optionId) {
                    DB::table('product_variants')
                        ->where('id', $variant->id)
                        ->update(['packing_quantity_option_id' => $optionId]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_variants', 'packing_quantity_option_id')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropConstrainedForeignId('packing_quantity_option_id');
            });
        }
    }
};

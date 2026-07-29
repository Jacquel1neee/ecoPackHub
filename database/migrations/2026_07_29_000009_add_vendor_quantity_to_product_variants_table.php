<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_variants', 'vendor_quantity')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->integer('vendor_quantity')->nullable()->after('vendor_price');
            });
        }

        if (Schema::hasTable('product_vendor')) {
            $variants = DB::table('product_variants')
                ->select('id', 'product_id', 'vendor_id')
                ->whereNotNull('vendor_id')
                ->get();

            foreach ($variants as $variant) {
                $qty = DB::table('product_vendor')
                    ->where('product_id', $variant->product_id)
                    ->where('vendor_id', $variant->vendor_id)
                    ->value('quantity');

                if ($qty !== null) {
                    DB::table('product_variants')
                        ->where('id', $variant->id)
                        ->update(['vendor_quantity' => (int) $qty]);
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_variants', 'vendor_quantity')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('vendor_quantity');
            });
        }
    }
};
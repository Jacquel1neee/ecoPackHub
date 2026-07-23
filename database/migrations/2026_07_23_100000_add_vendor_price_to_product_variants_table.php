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
            $table->decimal('vendor_price', 10, 2)->nullable()->after('price');
        });

        if (Schema::hasTable('product_vendor')) {
            DB::table('product_variants')
                ->whereNotNull('vendor_id')
                ->orderBy('id')
                ->eachById(function ($variant) {
                    $vendorPrice = DB::table('product_vendor')
                        ->where('product_id', $variant->product_id)
                        ->where('vendor_id', $variant->vendor_id)
                        ->value('price');

                    if ($vendorPrice !== null) {
                        DB::table('product_variants')
                            ->where('id', $variant->id)
                            ->update(['vendor_price' => $vendorPrice]);
                    }
                });
        }

        DB::table('product_variants')
            ->whereNull('vendor_price')
            ->update(['vendor_price' => DB::raw('price')]);
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('vendor_price');
        });
    }
};
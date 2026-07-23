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
            $table->foreignId('vendor_id')
                ->nullable()
                ->after('product_id')
                ->constrained('vendors')
                ->nullOnDelete();
        });

        // Preserve an existing product vendor assignment where possible.
        if (Schema::hasTable('product_vendor')) {
            DB::table('product_variants')
                ->whereNull('vendor_id')
                ->orderBy('id')
                ->eachById(function ($variant) {
                    $vendorId = DB::table('product_vendor')
                        ->where('product_id', $variant->product_id)
                        ->orderByDesc('is_preferred')
                        ->orderBy('id')
                        ->value('vendor_id');

                    if ($vendorId) {
                        DB::table('product_variants')
                            ->where('id', $variant->id)
                            ->update(['vendor_id' => $vendorId]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn('vendor_id');
        });
    }
};
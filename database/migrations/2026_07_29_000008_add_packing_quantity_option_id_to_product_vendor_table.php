<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_vendor', 'packing_quantity_option_id')) {
            Schema::table('product_vendor', function (Blueprint $table) {
                $table->foreignId('packing_quantity_option_id')
                    ->nullable()
                    ->after('quantity')
                    ->constrained('packing_quantity_options')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasColumn('product_vendor', 'packing_quantity_option_id')) {
            $variantRows = DB::table('product_variants')
                ->select('product_id', 'vendor_id', 'packing_quantity_option_id')
                ->whereNotNull('packing_quantity_option_id')
                ->groupBy('product_id', 'vendor_id', 'packing_quantity_option_id')
                ->get();

            foreach ($variantRows as $row) {
                DB::table('product_vendor')
                    ->where('product_id', $row->product_id)
                    ->where('vendor_id', $row->vendor_id)
                    ->update(['packing_quantity_option_id' => $row->packing_quantity_option_id]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_vendor', 'packing_quantity_option_id')) {
            Schema::table('product_vendor', function (Blueprint $table) {
                $table->dropConstrainedForeignId('packing_quantity_option_id');
            });
        }
    }
};
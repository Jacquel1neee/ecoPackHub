<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('order_items', 'variant_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('cascade')->after('order_id');
            });
        }

        if (Schema::hasColumn('order_items', 'product_id')) {
            $rows = DB::table('order_items')
                ->whereNull('variant_id')
                ->whereNotNull('product_id')
                ->get();

            foreach ($rows as $row) {
                $variant = DB::table('product_variants')
                    ->where('product_id', $row->product_id)
                    ->orderBy('id')
                    ->first();

                if ($variant) {
                    DB::table('order_items')
                        ->where('id', $row->id)
                        ->update(['variant_id' => $variant->id]);
                }
            }

            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('order_items', 'product_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade')->after('order_id');
            });
        }

        if (Schema::hasColumn('order_items', 'variant_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['variant_id']);
                $table->dropColumn('variant_id');
            });
        }
    }
};
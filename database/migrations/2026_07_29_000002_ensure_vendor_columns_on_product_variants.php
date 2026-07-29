<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_variants', 'vendor_id')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->foreignId('vendor_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('vendors')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('product_variants', 'vendor_price')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->decimal('vendor_price', 10, 2)->nullable()->after('price');
            });
        }

        if (Schema::hasTable('product_vendor')) {
            DB::table('product_variants')
                ->whereNull('vendor_id')
                ->orderBy('id')
                ->eachById(function ($variant) {
                    $preferredAssignment = DB::table('product_vendor')
                        ->where('product_id', $variant->product_id)
                        ->orderByDesc('is_preferred')
                        ->orderBy('id')
                        ->first();

                    if (! $preferredAssignment) {
                        return;
                    }

                    DB::table('product_variants')
                        ->where('id', $variant->id)
                        ->update([
                            'vendor_id' => $preferredAssignment->vendor_id,
                            'vendor_price' => $preferredAssignment->price,
                        ]);
                });
        }

        DB::table('product_variants')
            ->whereNull('vendor_price')
            ->update([
                'vendor_price' => DB::raw('price'),
            ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_variants', 'vendor_price')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('vendor_price');
            });
        }

        if (Schema::hasColumn('product_variants', 'vendor_id')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropConstrainedForeignId('vendor_id');
            });
        }
    }
};
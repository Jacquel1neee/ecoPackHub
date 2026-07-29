<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'show_price_on_homepage')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('show_price_on_homepage')->default(true)->after('is_discount_active');
            });
        }

        DB::table('products')->whereNull('show_price_on_homepage')->update(['show_price_on_homepage' => true]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'show_price_on_homepage')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('show_price_on_homepage');
            });
        }
    }
};
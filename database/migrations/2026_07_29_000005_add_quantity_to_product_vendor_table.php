<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_vendor', 'quantity')) {
            Schema::table('product_vendor', function (Blueprint $table) {
                $table->decimal('quantity', 10, 2)->nullable()->after('price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_vendor', 'quantity')) {
            Schema::table('product_vendor', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }
};
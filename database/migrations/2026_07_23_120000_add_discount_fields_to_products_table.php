<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('discount_price', 10, 2)->nullable()->after('product_group');
            $table->decimal('discount_percentage', 5, 2)->nullable()->after('discount_price');
            $table->boolean('is_discount_active')->default(false)->after('discount_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_price', 'discount_percentage', 'is_discount_active']);
        });
    }
};

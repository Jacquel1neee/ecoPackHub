<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add delivery method field (shipping / self pickup)
            if (!Schema::hasColumn('orders', 'delivery_method')) {
                $table->enum('delivery_method', ['shipping', 'selfpickup'])
                      ->default('shipping')
                      ->after('status');
            }
            
            // Add payment status field
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])
                      ->default('pending')
                      ->after('delivery_method');
            }
            
            // Add phone number field if not exists
            if (!Schema::hasColumn('orders', 'phone')) {
                $table->string('phone')->nullable()->after('shipping_address');
            }
            
            // Add order notes field if not exists
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_method', 'payment_status', 'phone', 'notes']);
        });
    }
};
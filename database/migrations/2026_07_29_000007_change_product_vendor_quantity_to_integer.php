<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_vendor', 'quantity')) {
            return;
        }

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE product_vendor MODIFY quantity INT NULL');
        }

        DB::table('product_vendor')
            ->whereNotNull('quantity')
            ->update(['quantity' => DB::raw('ROUND(quantity)')]);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('product_vendor', 'quantity')) {
            return;
        }

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE product_vendor MODIFY quantity DECIMAL(10,2) NULL');
        }
    }
};
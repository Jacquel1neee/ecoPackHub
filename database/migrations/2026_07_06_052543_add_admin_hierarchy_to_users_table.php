<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'promoted_by')) {
                $table->foreignId('promoted_by')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null')
                    ->after('role');
            }

            if (!Schema::hasColumn('users', 'level')) {
                $table->tinyInteger('level')->default(0)->after('promoted_by');
            }

            if (!Schema::hasColumn('users', 'last_sales_check')) {
                $table->timestamp('last_sales_check')->nullable()->after('level');
            }

            if (!Schema::hasColumn('users', 'path')) {
                $table->string('path')->nullable()->after('last_sales_check');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['promoted_by']);
            $table->dropColumn(['promoted_by', 'level', 'last_sales_check', 'path']);
        });
    }
};
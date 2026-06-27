<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiries', 'last_reply_at')) {
                $table->timestamp('last_reply_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('enquiries', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('last_reply_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['last_reply_at', 'is_read']);
        });
    }
};
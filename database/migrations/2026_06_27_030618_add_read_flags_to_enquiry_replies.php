<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiry_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiry_replies', 'is_read_by_admin')) {
                $table->boolean('is_read_by_admin')->default(false)->after('sender_name');
            }
            if (!Schema::hasColumn('enquiry_replies', 'is_read_by_user')) {
                $table->boolean('is_read_by_user')->default(false)->after('is_read_by_admin');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enquiry_replies', function (Blueprint $table) {
            $table->dropColumn(['is_read_by_admin', 'is_read_by_user']);
        });
    }
};
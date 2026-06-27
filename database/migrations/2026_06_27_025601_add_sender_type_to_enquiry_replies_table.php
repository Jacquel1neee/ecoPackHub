<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiry_replies', function (Blueprint $table) {
            $table->enum('sender_type', ['admin', 'user'])->default('admin')->after('admin_id');
            $table->string('sender_name')->nullable()->after('sender_type');
        });
    }

    public function down(): void
    {
        Schema::table('enquiry_replies', function (Blueprint $table) {
            $table->dropColumn(['sender_type', 'sender_name']);
        });
    }
};
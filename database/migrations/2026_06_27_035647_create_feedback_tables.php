<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== 先检查表是否存在，如果存在则跳过 =====
        if (!Schema::hasTable('feedbacks')) {
            Schema::create('feedbacks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('name');
                $table->string('email');
                $table->string('subject');
                $table->text('message');
                $table->string('status')->default('pending');
                $table->boolean('is_read')->default(false);
                $table->timestamp('last_reply_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('feedback_replies')) {
            Schema::create('feedback_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('feedback_id')->constrained('feedbacks')->onDelete('cascade');
                $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
                $table->text('reply_message');
                $table->enum('sender_type', ['admin', 'user'])->default('admin');
                $table->string('sender_name')->nullable();
                $table->boolean('is_read_by_admin')->default(false);
                $table->boolean('is_read_by_user')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_replies');
        Schema::dropIfExists('feedbacks');
    }
};
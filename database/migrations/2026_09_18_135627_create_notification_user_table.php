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
        if (! Schema::hasTable('notification_user')) {
            Schema::create('notification_user', function (Blueprint $table) {
                $table->id();

                // ربط الإشعار بـ UUID متطابق مع notifications->id
                $table->foreignUuid('notification_id')
                      ->constrained('notifications')
                      ->cascadeOnDelete();

                // ربط المستخدم بـ BigInteger متطابق مع users->id
                $table->foreignId('user_id')
                      ->constrained('users')
                      ->cascadeOnDelete();

                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->unique(['notification_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_user');
    }
};

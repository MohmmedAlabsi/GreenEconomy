<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // المستخدم المستهدف (إن وجد)
            $table->string('title');                                                   // عنوان الإشعار
            $table->text('body');                                                      // نص الإشعار
            $table->string('audience')->default('all');                                // الجمهور (all, farmers, engineers)
            $table->string('priority')->default('normal');                             // الأولوية (low, normal, high)
            $table->boolean('is_read')->default(false);                                // حالة القراءة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
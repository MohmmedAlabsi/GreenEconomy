<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable'); // ينشئ تلقائياً notifiable_id و notifiable_type
            $table->string('priority')->default('normal');
            $table->text('data');          // يحفظ نص الإشعار، العنوان، الروابط، وأي تفاصيل أخرى
            $table->timestamp('read_at')->nullable(); // تاريخ القراءة (null = غير مقروء)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
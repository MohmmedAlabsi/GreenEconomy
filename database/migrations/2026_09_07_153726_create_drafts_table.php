<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // مثل 'visit' أو 'feasibility'
            $table->json('payload'); // يحتوي على جميع حقول النموذج المدخلة
            $table->timestamps();

            // منع تكرار المسودة لنفس المستخدم والنوع
            $table->unique(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drafts');
    }
};
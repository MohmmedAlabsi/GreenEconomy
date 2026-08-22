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
        Schema::table('field_visits', function (Blueprint $table) {
            // إضافة معرّف المهندس المكلف بالزيارة
            $table->foreignId('engineer_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('users')
                  ->nullOnDelete();

            // إضافة رقم مرحلة الرحلة (من 1 إلى 9)
            $table->unsignedTinyInteger('current_step')
                  ->default(1)
                  ->after('status');

            // إضافة حقول التقييم لخدمة المزارع (المرحلة 9)
            $table->unsignedTinyInteger('rating')->nullable()->after('estimated_cost');
            $table->text('rating_comment')->nullable()->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->dropForeign(['engineer_id']);
            $table->dropColumn([
                'engineer_id',
                'current_step',
                'rating',
                'rating_comment',
            ]);
        });
    }
};
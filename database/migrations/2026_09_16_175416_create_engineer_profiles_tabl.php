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
        Schema::create('engineer_profiles', function (Blueprint $table) {
            $table->id();

            // الربط مع جدول المستخدمين (في حال حذف المستخدم يُحذف بروفايله تلقائياً)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // الربط مع التخصص
            $table->foreignId('specialization_id')->nullable()->constrained('specializations')->nullOnDelete();

            // البيانات المهنية والوثائق
            $table->integer('years_of_experience')->default(0);  // سنوات الخبرة
            $table->string('qualification')->nullable();         // الدرجة العلمية / المؤهل
            $table->text('bio')->nullable();                     // السيرة الذاتية النصية / نبذة
            $table->string('cv_file')->nullable();               // رابط/ملف الـ CV

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineer_profiles');
    }
};
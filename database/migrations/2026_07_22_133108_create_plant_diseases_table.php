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
        Schema::disableForeignKeyConstraints();

        Schema::create('plant_diseases', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);                              // اسم المرض
            $table->string('scientific_name', 255)->nullable();       // الاسم العلمي
            $table->string('plant_type', 255)->nullable();            // نوع المحصول / النبات
            $table->string('type', 100)->nullable();                  // التصنيف (فطري، بكتيري...)
            $table->string('severity_level', 50)->nullable();        // مستوى الخطورة
            $table->string('spread_rate', 50)->nullable();           // سرعة انتشار المرض
            $table->string('farmer_visibility', 100)->default('مرئي للمزارعين'); // العرض للمزارعين
            $table->text('symptoms');                                // وصف تفصيلي للأعراض
            $table->text('cause_description')->nullable();           // أسباب المرض
            $table->string('image_url', 255)->nullable();             // مسار الصورة المحفوظة
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_diseases');
    }
};
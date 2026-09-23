<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_visit_reports', function (Blueprint $table) {
            $table->id();
            
            // الربط مع طلب الزيارة والمهندس كاتب التقرير
            $table->foreignId('field_visit_id')->constrained('field_visits')->cascadeOnDelete();
            $table->foreignId('engineer_id')->constrained('users')->cascadeOnDelete();

            // تفاصيل التقرير الميداني
            $table->text('diagnosis');              // التشخيص النهائي للمشكلة
            $table->text('recommendations');         // التوصيات وخطة العلاج
            $table->text('prescribed_inputs')->nullable(); // المبيدات والأسمدة الموصى بها
            $table->text('notes')->nullable();       // ملاحظات إضافية
            
            // إضافة عمود لتخزين رابط المرفق القادم من Supabase Storage
            $table->text('attachment')->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_visit_reports');
    }
};
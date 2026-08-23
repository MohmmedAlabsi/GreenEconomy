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
        Schema::create('engineer_join_requests', function (Blueprint $table) {
            $table->id();
            
            // 👈 البيانات العامة للمستخدم (التي ستُنتقل لاحقاً إلى جدول users عند القبول)
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->foreignId('role_id')->constrained('roles'); // دور المهندس
            $table->foreignId('region_id')->constrained('regions');
            $table->string('district');

            // 👈 البيانات المهنية (التي ستُنتقل لاحقاً إلى جدول EngineerProfile عند القبول)
            $table->foreignId('specialization_id')->constrained('specializations');
            $table->string('qualification');
            $table->integer('years_of_experience');
            $table->text('bio')->nullable();
            $table->string('cv_file')->nullable();
            
            // 👈 حالة الطلب والملاحظات
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable(); // سبب الرفض أو ملاحظات الإدمن
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineer_join_requests');
    }
};

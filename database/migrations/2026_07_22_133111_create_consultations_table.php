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

        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('issue_title', 255);
            $table->string('crop_type', 100);
            $table->string('crop_age', 100);
            $table->string('issue_duration', 100);
            $table->text('description');
            $table->string('status', 50)->nullable()->default('pending');
            $table->unsignedBigInteger('assigned_expert_id')->nullable();
            $table->foreign('assigned_expert_id')->references('id')->on('users');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};

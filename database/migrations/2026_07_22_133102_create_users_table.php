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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('phone', 20)->unique()->nullable();
            $table->string('email', 255)->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->string('avatar', 255)->nullable();
            $table->string('governorate', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('crop_types', 255)->nullable();
            $table->string('membership_tier', 50)->nullable()->default('standard');
            $table->string('status', 50)->default('active');
            $table->boolean('identity_verified')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles');
            $table->unsignedBigInteger('region_id')->nullable();
            $table->foreign('region_id')->references('id')->on('regions');
            $table->unsignedBigInteger('specialization_id')->nullable();
            $table->foreign('specialization_id')->references('id')->on('specializations');
            $table->string('remember_token', 100)->nullable();
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
        Schema::dropIfExists('users');
    }
};

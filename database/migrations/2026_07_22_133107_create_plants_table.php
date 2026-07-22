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

        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->string('common_name', 255);
            $table->string('scientific_name', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('climate_requirements')->nullable();
            $table->text('irrigation_schedule')->nullable();
            $table->string('planting_season', 100)->nullable();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->string('image_url', 255)->nullable();
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
        Schema::dropIfExists('plants');
    }
};

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
            $table->string('name', 255);
            $table->string('scientific_name', 255)->nullable();
            $table->string('type', 50);
            $table->text('symptoms');
            $table->text('cause_description')->nullable();
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
        Schema::dropIfExists('plant_diseases');
    }
};

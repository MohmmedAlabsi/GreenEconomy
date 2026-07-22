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

        Schema::create('plant_disease_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plant_id');
            $table->foreign('plant_id')->references('id')->on('plants');
            $table->unsignedBigInteger('disease_id');
            $table->foreign('disease_id')->references('id')->on('plant_diseases');
            $table->unique(['plant_id', 'disease_id']);
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_disease_pivot');
    }
};

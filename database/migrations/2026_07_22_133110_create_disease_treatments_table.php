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

        Schema::create('disease_treatments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('disease_id');
            $table->foreign('disease_id')->references('id')->on('plant_diseases');
            $table->string('treatment_type', 50);
            $table->string('title', 255);
            $table->text('instructions');
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
        Schema::dropIfExists('disease_treatments');
    }
};

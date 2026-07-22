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

        Schema::create('field_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('contact_name', 255);
            $table->string('contact_phone', 20);
            $table->string('governorate', 100);
            $table->string('district', 100);
            $table->string('village_or_area', 255);
            $table->string('nearest_landmark', 255)->nullable();
            $table->string('crop_type', 100);
            $table->decimal('area_size', 10, 2);
            $table->string('infestation_type', 150);
            $table->string('priority_level', 50);
            $table->text('problem_description');
            $table->string('status', 50)->nullable()->default('submitted');
            $table->timestamp('scheduled_at')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable()->default(0);
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
        Schema::dropIfExists('field_visits');
    }
};

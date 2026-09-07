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

        Schema::create('feasibility_studies', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->unsignedBigInteger('region_id')->nullable();
            $table->foreign('region_id')->references('id')->on('regions');
            $table->text('cover_image')->nullable()->change();
            $table->decimal('capital_required', 15, 2)->nullable();
            $table->decimal('expected_roi', 5, 2);
            $table->integer('payback_period')->nullable();
            $table->string('risk_level', 50)->nullable();
            $table->string('status', 50)->default('draft');
            $table->text('pdf_file')->nullable()->change();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::table('feasibility_studies', function (Blueprint $table) {
            $table->string('cover_image', 255)->nullable()->change();
            $table->string('pdf_file', 255)->nullable()->change();
        });
    }
};

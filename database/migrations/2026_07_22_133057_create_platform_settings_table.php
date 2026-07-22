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
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('platform_name', 255)->default('Terra Admin');
            $table->string('default_language', 10)->default('en');
            $table->string('timezone', 100)->default('Asia/Dubai');
            $table->string('date_format', 50)->default('DD/MM/YYYY');
            $table->string('production_api_key', 255)->nullable();
            $table->string('test_api_key', 255)->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};

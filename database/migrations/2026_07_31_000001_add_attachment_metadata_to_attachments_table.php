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
        Schema::table('attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('attachable_id');
            $table->string('file_name')->nullable()->after('user_id');
            $table->unsignedInteger('file_size')->nullable()->after('file_name');
            $table->string('url')->nullable()->after('file_size');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'file_name', 'file_size', 'url']);
        });
    }
};

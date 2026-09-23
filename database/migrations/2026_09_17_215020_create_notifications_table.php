<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type')->default('App\\Notifications\\GeneralNotification');
                $table->morphs('notifiable'); // notifiable_id و notifiable_type
                $table->string('priority')->default('normal'); // للحفاظ على أولويات الواجهة
                $table->text('data'); // بيانات الإشعار (العنوان، الرسالة، الرابط)
                $table->timestamp('read_at')->nullable(); // حالة القراءة
                $table->timestamps();
            });

            return;
        }

        // A stock Laravel `notifications` table (php artisan make:notifications-table) already
        // exists: keep it (its `id` is already a UUID, which notification_user relies on) and
        // only add the custom column the frontend needs.
        if (! Schema::hasColumn('notifications', 'priority')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('priority')->default('normal')->after('notifiable_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

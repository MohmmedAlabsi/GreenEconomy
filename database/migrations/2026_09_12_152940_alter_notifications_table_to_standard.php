<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. إنشاء جدول إشعارات جديد بالهيكل القياسي
        Schema::create('notifications_temp', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->default('App\\Notifications\\GeneralNotification');
            $table->morphs('notifiable');
            $table->string('priority')->default('normal');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // 2. قراءة وترحيل البيانات القديمة بدقة وتحويلها لـ JSON و UUID
        if (Schema::hasTable('notifications')) {
            $oldNotifications = DB::table('notifications')->get();

            foreach ($oldNotifications as $item) {
                $payload = [
                    'title'    => $item->title ?? '',
                    'body'     => $item->body ?? '',
                    'audience' => $item->audience ?? 'specific',
                    'priority' => $item->priority ?? 'normal',
                ];

                DB::table('notifications_temp')->insert([
                    'id'              => (string) Str::uuid(),
                    'type'            => 'App\\Notifications\\GeneralNotification',
                    'notifiable_type' => 'App\\Models\\User',
                    'notifiable_id'   => $item->user_id ?? 1, // إسناده للـ user_id القديم أو الأدمن
                    'priority'        => $item->priority ?? 'normal',
                    'data'            => json_encode($payload, JSON_UNESCAPED_UNICODE),
                    'read_at'         => (!empty($item->is_read) && $item->is_read == 1) ? ($item->updated_at ?? now()) : null,
                    'created_at'      => $item->created_at ?? now(),
                    'updated_at'      => $item->updated_at ?? now(),
                ]);
            }

            // 3. حذف الجدول القديم
            Schema::dropIfExists('notifications');
        }

        // 4. إعادة تسمية الجدول المؤقت ليصبح هو notifications الأساسي
        Schema::rename('notifications_temp', 'notifications');
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
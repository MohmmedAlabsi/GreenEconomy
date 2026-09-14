<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public static function send(array $data, array $recipientIds = [], string $audience = 'specific')
    {
        return DB::transaction(function () use ($data, $recipientIds, $audience) {
            // 1. إنشاء سجل الإشعار
            $notificationId = DB::table('notifications')->insertGetId([
                'title'      => $data['title'],
                'body'       => $data['body'],
                'priority'   => $data['priority'] ?? 'normal',
                'audience'   => $audience,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. تحديد المستلمين
            if ($audience === 'all') {
                $recipientIds = User::pluck('id')->toArray();
            } elseif ($audience === 'farmers') {
                $recipientIds = User::role('farmer')->pluck('id')->toArray();
            } elseif ($audience === 'engineers') {
                $recipientIds = User::role('engineer')->pluck('id')->toArray();
            } elseif ($audience === 'admins') {
                $recipientIds = User::role('admin')->pluck('id')->toArray();
            }

            // 3. ربط كل مستخدم بالإشعار بحالة غير مقروء خاصة به
            $records = [];
            foreach (array_unique($recipientIds) as $uid) {
                $records[] = [
                    'notification_id' => $notificationId,
                    'user_id'         => $uid,
                    'is_read'         => false,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            if (!empty($records)) {
                DB::table('notification_user')->insert($records);
            }

            return $notificationId;
        });
    }
}
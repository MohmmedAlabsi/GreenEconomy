<?php

namespace App\Http\Controllers\Notification;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\DB;

class NotificationController extends \App\Http\Controllers\Controller
{
    // جلب الإشعارات الواردة لحساب المستخدم الحالي فقط (للجرس والوارد)
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }

        $notifications = $user->notifications()->latest()->paginate(20);

        $notifications->getCollection()->transform(function ($item) {
            $data = is_array($item->data) ? $item->data : json_decode($item->data, true);
            return [
                'id'         => $item->id,
                'title'      => $data['title'] ?? 'إشعار جديد',
                'body'       => $data['body'] ?? '',
                'priority'   => $item->priority ?? $data['priority'] ?? 'normal',
                'type'       => $data['type'] ?? 'general',
                'data'       => $data,
                'is_read'    => $item->read_at !== null,
                'read_at'    => $item->read_at,
                'created_at' => $item->created_at,
            ];
        });

        return response()->json([
            'unread_count'  => $user->unreadNotifications()->count(),
            'notifications' => $notifications
        ]);
    }

    // تحديث حالة القراءة (يدعم POST و PATCH)
    public function markAsRead(Request $request, string $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'غير مصرح'], 401);
        }

        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        } else {
            // محاولة التحديث المباشر برمجياً عبر DB للتأكد
            DB::table('notifications')
                ->where('id', $id)
                ->where('notifiable_id', $user->id)
                ->update([
                    'read_at' => now(),
                    'updated_at' => now()
                ]);
        }

        return response()->json(['message' => 'تم تحديث حالة الإشعار بنجاح']);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return response()->json(['message' => 'تم تحديد جميع الإشعارات كمقروءة']);
    }

    // إرسال إشعار من لوحة الأدمن
    public function send(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'body'     => 'required|string',
            'audience' => 'required|in:specific,all,farmers,engineers,admins',
            'user_id'  => 'nullable',
            'priority' => 'nullable|in:low,normal,high,urgent',
        ]);

        $audience = $validated['audience'];
        $sender = $request->user();
        $targets = collect();
        $targetUserName = null;
        $targetUserRole = null;

        if ($audience === 'all') {
            $targets = User::all();
        } elseif ($audience === 'engineers') {
            $targets = User::where('role_id', 3)
                ->orWhereHas('role', fn($q) => $q->where('name', 'engineer'))
                ->orWhereHas('roles', fn($q) => $q->where('name', 'engineer'))
                ->get();
        } elseif ($audience === 'farmers') {
            $targets = User::where('role_id', 2)
                ->orWhereHas('role', fn($q) => $q->where('name', 'farmer'))
                ->orWhereHas('roles', fn($q) => $q->where('name', 'farmer'))
                ->get();
        } elseif ($audience === 'admins') {
            $targets = User::admins()->get();
        } elseif ($audience === 'specific' && !empty($validated['user_id'])) {
            $targets = User::with('role')->where('id', $validated['user_id'])->get();
            $targetUser = $targets->first();

            if ($targetUser) {
                $targetUserName = $targetUser->name;

                $roleName = strtolower((string) ($targetUser->role?->name ?? ''));
                $targetUserRole = (str_contains($roleName, 'engineer') || (string) $targetUser->role_id === '3')
                    ? 'engineer'
                    : 'farmer';
            }
        }

        if ($targets->isEmpty()) {
            return response()->json(['message' => 'لم يتم العثور على مستخدمين'], 422);
        }

        NotificationFacade::send($targets, new GeneralNotification([
            'title'            => $validated['title'],
            'body'             => $validated['body'],
            'priority'         => $validated['priority'] ?? 'normal',
            'audience'         => $audience,
            'target_user_name' => $targetUserName,
            'target_user_role' => $targetUserRole,
            'sender_id'        => $sender?->id,
            'sender_name'      => $sender?->name ?? 'الإدارة',
            'sender_role'      => 'admin',
            'type'             => $audience === 'specific' ? 'direct' : 'broadcast',
        ]));

        return response()->json(['message' => 'تم إرسال الإشعار بنجاح'], 201);
    }
}
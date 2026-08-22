<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * جلب كل الإشعارات مع بيانات المستخدم (role_id)
     */
    public function index(Request $request)
    {
        // تم إضافة 'name' هنا ليقوم الباك إند بإرسال اسم المستخدم مباشرة ضمن كائن user
        $notifications = Notification::with(['user:id,name,role_id'])->latest()->get();

        return response()->json([
            'status' => 'success',
            'data'   => $notifications
        ]);
    }

    /**
     * إرسال وحفظ إشعار جديد
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'audience' => 'required|string',
            'title'    => 'required|string|max:255',
            'body'     => 'required|string',
            'priority' => 'nullable|string',
            'user_id'  => 'required_if:audience,specific|nullable',       
        ]);

        // إذا تم إرسال البريد الإلكتروني بدلاً من الـ ID للمستخدم المخصص، نقوم بتحويله للـ ID الصحيح
        if ($request->audience === 'specific' && !is_numeric($request->user_id)) {
            $user = \App\Models\User::where('email', $request->user_id)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'المستخدم غير موجود.',
                    'errors' => ['user_id' => ['البريد الإلكتروني المدخل غير مسجل في النظام.']]
                ], 422);
            }
            $validated['user_id'] = $user->id;
        }

        $notification = Notification::create($validated);

        return response()->json([
            'message' => 'Notification created successfully',
            'data'    => $notification
        ], 201);
    }

    /**
     * تعليم الإشعار كمقروء
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json([
            'message' => 'Notification marked as read',
            'data'    => $notification
        ]);
    }
}
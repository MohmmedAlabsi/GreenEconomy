<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * جلب قائمة الإشعارات
     */
    public function index(Request $request)
    {
        $notifications = Notification::latest()->paginate(15);

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
            'user_id'  => 'nullable|exists:users,id',
        ]);

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
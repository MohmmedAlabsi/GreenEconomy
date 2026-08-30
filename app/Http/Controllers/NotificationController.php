<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Requests\StoreNotificationRequest;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::with(['user:id,name,role_id'])->latest()->get();

        return response()->json([
            'status' => 'success',
            'data'   => $notifications
        ]);
    }

    public function send(StoreNotificationRequest $request)
    {
        $notification = Notification::create($request->validated());

        return response()->json([
            'message' => 'Notification created successfully',
            'data'    => $notification
        ], 201);
    }

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
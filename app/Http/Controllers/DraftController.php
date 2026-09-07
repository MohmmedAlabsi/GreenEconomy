<?php

namespace App\Http\Controllers;

use App\Models\Draft;
use Illuminate\Http\Request;

class DraftController extends Controller
{
    // جلب المسودة الحالية للمستخدم
    public function show(Request $request)
    {
        $type = $request->query('type', 'visit');
        $draft = Draft::where('user_id', $request->user()->id)
            ->where('type', $type)
            ->first();

        return response()->json([
            'data' => $draft ? $draft->payload : null
        ]);
    }

    // حفظ أو تحديث المسودة
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'payload' => 'required|array',
        ]);

        $draft = Draft::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
            ],
            [
                'payload' => $validated['payload'],
            ]
        );

        return response()->json([
            'message' => 'تم حفظ المسودة بنجاح',
            'data' => $draft->payload
        ]);
    }

    // حذف المسودة بعد إرسال الطلب بنجاح أو عند الضغط على تجاهل
    public function destroy(Request $request)
    {
        $type = $request->query('type', 'visit');
        Draft::where('user_id', $request->user()->id)
            ->where('type', $type)
            ->delete();

        return response()->json([
            'message' => 'تم حذف المسودة بنجاح'
        ]);
    }
}
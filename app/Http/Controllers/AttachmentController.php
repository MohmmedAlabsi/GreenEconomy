<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StoreAttachmentRequest;

class AttachmentController extends Controller
{
    /**
     * رفع ملف جديد وتخزينه في النظام.
     */
    public function store(StoreAttachmentRequest $request) // استخدمنا الـ Request المخصص[cite: 3]
    {

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('attachments', $fileName, 'public');

            $attachableType = $request->input('attachable_type', User::class);
            $attachableId = $request->input('attachable_id', Auth::id() ?? 0);

            $attachment = Attachment::create([
                'attachable_type' => $attachableType,
                'attachable_id' => $attachableId,
                'user_id' => Auth::id(),
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $request->input('file_type', $file->getClientMimeType()),
                'file_size' => $file->getSize(),
                'url' => asset('storage/' . $filePath),
            ]);

            return response()->json([
                'message' => 'تم رفع الملف بنجاح',
                'data' => $attachment,
            ], 201);
        }

        return response()->json(['message' => 'حدث خطأ أثناء رفع الملف'], 400);
    }

    /**
     * عرض تفاصيل مرفق معين.
     */
    public function show($id)
    {
        if ($response = $this->ensureAuthenticated()) {
            return $response;
        }

        $attachment = Attachment::find($id);

        if (! $attachment) {
            return response()->json(['message' => 'المرفق غير موجود'], 404);
        }

        return response()->json(['data' => $attachment], 200);
    }

    /**
     * حذف مرفق من السيرفر وقاعدة البيانات.
     */
    public function destroy($id)
    {
        if ($response = $this->ensureAuthenticated()) {
            return $response;
        }

        $attachment = Attachment::find($id);

        if (! $attachment) {
            return response()->json(['message' => 'المرفق غير موجود'], 404);
        }

        if ($attachment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'غير مصرح لك بحذف هذا المرفق.',
            ], 403);
        }

        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return response()->json(['message' => 'تم حذف المرفق بنجاح'], 200);
    }

    private function ensureAuthenticated()
    {
        if (! Auth::check()) {
            return response()->json([
                'message' => 'Token مفقود أو غير صالح. يرجى تسجيل الدخول وإرسال توكن صالح في رأس الطلب.',
            ], 401);
        }

        return null;
    }
    
}

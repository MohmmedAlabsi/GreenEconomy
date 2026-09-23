<?php

namespace App\Http\Controllers\Engineer;
use App\Models\EngineerJoinRequest;
use App\Models\User;
use App\Models\EngineerProfile;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\Engineer\RejectEngineerJoinRequest;
use Illuminate\Support\Facades\Storage;

class AdminEngineerController extends \App\Http\Controllers\Controller
{
    // 1. جلب قائمة طلبات الانضمام المعلقة لعرضها في لوحة التحكم
    public function indexRequests()
    {
        $requests = EngineerJoinRequest::with(['specialization', 'region'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data'   => $requests
        ]);
    }

    // 2. قبول الطلب ونقل البيانات وتفعيل الحساب
    public function approveRequest(Request $request, $id)
    {
        $joinRequest = EngineerJoinRequest::findOrFail($id);

        // أ. إنشاء حساب المستخدم
        $user = User::create([
            'name'      => $joinRequest->name,
            'email'     => $joinRequest->email,
            'password'  => $joinRequest->password, 
            'phone'     => $joinRequest->phone,
            'role_id'   => $joinRequest->role_id ?? 3,
            'region_id' => $joinRequest->region_id,
            'district'  => $joinRequest->district,
            'status'    => 'active',
        ]);

        // إسناد الدور إن وجد
        try {
            if (class_exists(Role::class) && method_exists($user, 'assignRole')) {
                $role = Role::find($joinRequest->role_id);
                if ($role) {
                    $user->assignRole($role->name);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('تعذر إسناد صلاحية Spatie: ' . $e->getMessage());
        }

        // ب. إنشاء الملف المهني للمهندس
        EngineerProfile::create([
            'user_id'             => $user->id,
            'specialization_id'   => $joinRequest->specialization_id,
            'qualification'       => $joinRequest->qualification,
            'years_of_experience' => $joinRequest->years_of_experience,
            'bio'                 => $joinRequest->bio,
            'cv_file'             => $joinRequest->cv_file,
        ]);

        // ج. إرسال الإشعار بدون استخدام auth() نهائياً
        try {
            // جلب الأدمن من كائن الطلب مباشرة $request->user()
            $adminUser = $request->user();
            $adminId = $adminUser ? $adminUser->id : null;
            $adminName = $adminUser ? $adminUser->name : 'الإدارة';

            $user->notify(new GeneralNotification([
                'title'            => 'تم قبول طلب الانضمام',
                'body'             => 'مبارك! تم قبول طلب انضمامك إلى المنصة كمهندس زراعي معتمد.',
                'priority'         => 'high',
                'audience'         => 'specific',
                'sender_id'        => $adminId,
                'sender_name'      => $adminName,
                'sender_role'      => 'admin',
                'target_user_name' => $user->name,
                'target_user_role' => 'engineer',
                'type'             => 'account_approval',
                'action_url'       => '/engineer',
            ]));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('تعذر إرسال إشعار القبول للمهندس: ' . $e->getMessage());
        }

        // د. حذف الطلب المؤقت
        $joinRequest->delete();

        return response()->json([
            'status'  => true,
            'message' => 'تم قبول الطلب بنجاح، ونقل بيانات المهندس وتفعيل حسابه.'
        ]);
    }
    public function rejectRequest(RejectEngineerJoinRequest $request, $id)
    {
        $joinRequest = EngineerJoinRequest::findOrFail($id);

        // 1. حذف ملف السيرة الذاتية (CV) من مساحة Supabase لتنظيف التخزين
        if ($joinRequest->cv_file && str_contains($joinRequest->cv_file, 'supabase.co')) {
            $oldCvPath = preg_replace('/^.*\/cv_files\//', 'cv_files/', $joinRequest->cv_file);
            Storage::disk('supabase')->delete($oldCvPath);
        }

        // 2. حذف الطلب نهائياً من السجلات
        $joinRequest->delete();

        return response()->json([
            'status'  => true,
            'message' => 'تم رفض الطلب وحذفه من السجلات بنجاح.'
        ]);
    }
}
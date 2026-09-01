<?php

namespace App\Http\Controllers;

use App\Models\EngineerJoinRequest;
use App\Models\User;
use App\Models\EngineerProfile;
use App\Models\Notification;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Requests\RejectEngineerJoinRequest;

class AdminEngineerController extends Controller
{
    // 1. جلب قائمة طلبات الانضمام المعلقة لعرضها في لوحة التحكم[cite: 10]
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

    // 2. قبول الطلب ونقل البيانات وتفريغ الجدول[cite: 10]
    public function approveRequest($id)
    {
        $joinRequest = EngineerJoinRequest::findOrFail($id);

        // أ. نقل البيانات العامة إلى جدول users وتفعيل الحساب[cite: 10]
        $user = User::create([
            'name'      => $joinRequest->name,
            'email'     => $joinRequest->email,
            'password'  => $joinRequest->password, 
            'phone'     => $joinRequest->phone,
            'role_id'   => $joinRequest->role_id,
            'region_id' => $joinRequest->region_id,
            'district'  => $joinRequest->district,
            'status'    => 'active',
        ]);

        // إسناد صلاحية Spatie للمهندس[cite: 10]
        $role = Role::find($joinRequest->role_id);
        if ($role) {
            $user->assignRole($role);
        }

        // ب. نقل البيانات المهنية إلى جدول EngineerProfile[cite: 10]
        EngineerProfile::create([
            'user_id'             => $user->id,
            'specialization_id'   => $joinRequest->specialization_id,
            'qualification'       => $joinRequest->qualification,
            'years_of_experience' => $joinRequest->years_of_experience,
            'bio'                 => $joinRequest->bio,
            'cv_file'             => $joinRequest->cv_file,
        ]);

        // ج. إرسال إشعار للمهندس بقبول طلبه
        Notification::create([
            'audience' => 'specific',
            'title'    => 'تم قبول طلب الانضمام',
            'body'     => 'مبارك! تم قبول طلب انضمامك إلى المنصة كمهندس زراعي بنجاح.',
            'priority' => 'high',
            'user_id'  => $user->id, // ربط الإشعار باليوزر الجديد
        ]);

        // د. تفريغ وحذف الطلب من جدول الطلبات المؤقتة[cite: 10]
        $joinRequest->delete();

        return response()->json([
            'status'  => true,
            'message' => 'تم قبول الطلب بنجاح، ونقل بيانات المهندس وتفعيل حسابه.'
        ]);
    }

    public function rejectRequest(RejectEngineerJoinRequest $request, $id)
    {
        $joinRequest = EngineerJoinRequest::findOrFail($id);

        // 1. إنشاء الإشعار بالرفض (كما في كودك الأصلي)
        Notification::create([
            'audience' => 'specific',
            'title'    => 'اعتذار عن قبول طلب الانضمام',
            'body'     => 'نأسف إبلاغك بأنه تم رفض طلب انضمامك للأسباب التالية: ' . $request->notes,
            'priority' => 'normal',
            'user_id'  => null, 
        ]);

        // 2. حذف ملف السيرة الذاتية (CV) من مساحة Supabase لتنظيف التخزين
        if ($joinRequest->cv_file && str_contains($joinRequest->cv_file, 'supabase.co')) {
            $oldCvPath = preg_replace('/^.*\/cv_files\//', 'cv_files/', $joinRequest->cv_file);
            \Illuminate\Support\Facades\Storage::disk('supabase')->delete($oldCvPath);
        }

        // 3. حذف الطلب نهائياً من السجلات
        $joinRequest->delete();

        return response()->json([
            'status'  => true,
            'message' => 'تم رفض الطلب وحذفه من السجلات بنجاح.'
        ]);
    }
}
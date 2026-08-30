<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EngineerJoinRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;

class AuthController extends Controller
{
    /**
     * تسجيل حساب جديد في جدول users أو حفظ طلب انضمام المهندس في جدول الطلبات المؤقتة
     */
    public function register(RegisterUserRequest $request) // استخدام الـ Request الخاص[cite: 4]
    {
        // 1. تحديد رقم دور المهندس ديناميكياً أو برقم ثابت (مثلاً 3)
        $engineerRoleId = 3; // استبدله بالرقم الفعلي لدور المهندس في جدول roles لديك

        // إذا كان المسجل مهندساً (حسب الـ role_id)
        if ($request->role_id == $engineerRoleId) {
            $cvPath = null;
            if ($request->hasFile('cv_file')) {
                $cvPath = $request->file('cv_file')->store('cv_files', 'public');
            }

            // حفظ الطلب في جدول طلبات المهندسين المؤقتة
            EngineerJoinRequest::create([
                'name'                => $request->name,
                'email'               => $request->email,
                'password'            => Hash::make($request->password),
                'phone'               => $request->phone,
                'role_id'             => $request->role_id,
                'region_id'           => $request->region_id,
                'district'            => $request->district,
                'specialization_id'   => $request->specialization_id,
                'qualification'       => $request->qualification,
                'years_of_experience' => $request->years_of_experience,
                'bio'                 => $request->bio,
                'cv_file'             => $cvPath,
                'status'              => 'pending',
            ]);

            // إرسال إشعار للإدمن
            Notification::create([
                'audience' => 'admin',
                'title'    => 'طلب انضمام مهندس جديد',
                'body'     => 'قام المهندس ' . $request->name . ' بتقديم طلب انضمام للمنصة، يرجى مراجعة الطلب.',
                'priority' => 'high',
                'user_id'  => null,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'تم إرسال طلب انضمامك بنجاح، وسيتم مراجعته من قبل الإدارة قريباً.'
            ], 201);
        }

        // أما إذا كان مزارعاً أو مستخدمًا عادياً (يتم إنشاء حسابه مباشرة)
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'role_id'   => $request->role_id,
            'region_id' => $request->region_id,
            'district'  => $request->district,
        ]);

        $role = Role::findById($request->role_id, 'api');
        if ($role) {
            $user->assignRole($role);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => true,
            'message'      => 'تم إنشاء الحساب بنجاح',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load(['role', 'roles'])
        ], 201);
    }

    /**
     * تسجيل الدخول[cite: 8]
     */
    public function login(LoginUserRequest $request) // استخدام الـ Request الخاص[cite: 4]
    {

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => true,
            'message'      => 'تم تسجيل الدخول بنجاح',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load(['role', 'roles'])
        ], 200);
    }

    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status'  => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ], 200);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * تسجيل حساب جديد في جدول users وإسناد دور Spatie تلقائياً
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed',
            'phone'     => 'nullable|string|max:20',
            'role_id'   => 'required|exists:roles,id',
            'region_id' => 'required|exists:regions,id',
            'district'  => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 1. إنشاء المستخدم داخل جدول users
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'role_id'   => $request->role_id,
            'region_id' => $request->region_id,
            'district'  => $request->district,
        ]);

        // 2. 👈 إسناد الدور في جدول Spatie (model_has_roles) تلقائياً
        $role = Role::findById($request->role_id, 'api');
        if ($role) {
            $user->assignRole($role);
        }

        // إنتاج Sanctum Token فور التسجيل
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => true,
            'message'      => 'تم إنشاء الحساب بنجاح',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load(['role', 'roles']) // إرجاع العلاقتين للـ Frontend
        ], 201);
    }

    /**
     * تسجيل الدخول
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

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
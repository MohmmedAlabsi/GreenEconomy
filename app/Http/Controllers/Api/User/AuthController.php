<?php

namespace App\Http\Controllers\User;
use App\Models\User;
use App\Models\EngineerJoinRequest;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\LoginUserRequest;

class AuthController extends \App\Http\Controllers\Controller
{
    public function register(RegisterUserRequest $request)
    {
        $engineerRoleId = 3;

        // إذا كان المسجل مهندساً
        if ($request->role_id == $engineerRoleId) {
            $cvFileUrl = null;

            if ($request->hasFile('cv_file') && $request->file('cv_file')->isValid()) {
                try {
                    $cvPath = $request->file('cv_file')->store('cv_files', 'supabase');
                    if ($cvPath) {
                        $baseUrl = rtrim(config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL') ?? '', '/');
                        $cvFileUrl = $baseUrl ? ($baseUrl . '/' . $cvPath) : $cvPath;
                    }
                } catch (\Throwable $e) {
                    Log::warning('تعذر الرفع إلى Supabase، جاري الحفظ المحلي: ' . $e->getMessage());
                }

                if (!$cvFileUrl) {
                    try {
                        $localPath = $request->file('cv_file')->store('cv_files', 'public');
                        if ($localPath) {
                            $cvFileUrl = asset('storage/' . $localPath);
                        }
                    } catch (\Throwable $e) {
                        Log::error('فشل الرفع المحلي للسيرة الذاتية: ' . $e->getMessage());
                    }
                }

                if (!$cvFileUrl) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'تعذر حفظ ملف السيرة الذاتية، يرجى التحقق من الملف وإعادة المحاولة.'
                    ], 500);
                }
            }

            $joinRequest = EngineerJoinRequest::create([
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
                'cv_file'             => $cvFileUrl,
                'status'              => 'pending',
            ]);

            $admins = User::where('role_id', 1)
                ->orWhereHas('role', fn($q) => $q->where('name', 'admin'))
                ->orWhereHas('roles', fn($q) => $q->where('name', 'admin'))
                ->get();

            if ($admins->isNotEmpty()) {
                NotificationFacade::send($admins, new GeneralNotification([
                    'title'       => 'طلب انضمام مهندس جديد',
                    'body'        => 'قام المهندس ' . $request->name . ' بتقديم طلب انضمام للمنصة، يرجى مراجعة الطلب.',
                    'priority'    => 'high',
                    'audience'    => 'admins',
                    'sender_name' => $request->name,
                    'sender_role' => 'engineer',
                    'type'        => 'engineer_join_request',
                ]));
            }

            return response()->json([
                'status'  => true,
                'message' => 'تم إرسال طلب انضمامك بنجاح، وسيتم مراجعته من قبل الإدارة قريباً.'
            ], 201);
        }

        // إذا كان مزارعاً أو مستخدماً عادياً
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'role_id'   => $request->role_id,
            'region_id' => $request->region_id,
            'district'  => $request->district,
        ]);

        if (class_exists(Role::class) && method_exists($user, 'assignRole')) {
            $role = Role::where('id', $request->role_id)->first();
            if ($role) {
                $user->assignRole($role);
            }
        }

        $admins = User::admins()->get();

        if ($admins->isNotEmpty()) {
            NotificationFacade::send($admins, new GeneralNotification([
                'title'       => 'تسجيل مزارع جديد',
                'body'        => 'قام المزارع ' . $user->name . ' بإنشاء حساب جديد في المنصة.',
                'priority'    => 'normal',
                'audience'    => 'admins',
                'sender_id'   => $user->id,
                'sender_name' => $user->name,
                'sender_role' => 'farmer',
                'type'        => 'new_user',
            ]));
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

    public function login(LoginUserRequest $request)
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

    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'user'   => $request->user()->load(['role', 'roles']),
        ]);
    }
}
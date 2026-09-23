<?php

namespace App\Http\Controllers\User;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Database\QueryException;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class UserController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        // إضافة engineerProfile.specialization لضمان جلب بيانات التخصص والبروفايل مع كل مستخدم
        $query = User::with(['region', 'role', 'engineerProfile.specialization']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('district', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->input('region_id'));
        }

        $users = $query->latest()->get();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with([
            'role', 
            'roles', 
            'region', 
            'engineerProfile.specialization', 
            'consultations'
        ])->findOrFail($id); //[cite: 31]

        return response()->json($user);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']); //[cite: 31]

        $user = User::create($validated); //[cite: 31]

        if (!empty($validated['role_id'])) {
            $role = Role::find($validated['role_id']); //[cite: 31]
            if ($role) {
                $user->assignRole($role); //[cite: 31]
            }
        }

        return response()->json([
            'message' => 'User created successfully',
            'data'    => $user->load(['role', 'roles', 'region', 'engineerProfile.specialization'])
        ], 201); //[cite: 31]
    }

        public function update(UpdateUserRequest $request, string $id)
        {
            $user = User::findOrFail($id); //[cite: 31]
            $validated = $request->validated();

            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']); //[cite: 31]
            }

            $user->update($validated); //[cite: 31]

            if (array_key_exists('role_id', $validated)) {
                if ($validated['role_id']) {
                    $role = Role::find($validated['role_id']); //[cite: 31]
                    if ($role) {
                        $user->syncRoles([$role]); //[cite: 31]
                    }
                } else {
                    $user->syncRoles([]); //[cite: 31]
                }
            }

            return response()->json([
                'message' => 'User updated successfully',
                'data'    => $user->load(['role', 'roles', 'region', 'engineerProfile.specialization'])
            ]); //[cite: 31]
        }

    public function destroy(Request $request, string $id)
    {
        try {
            $user = User::withTrashed()->find($id);

            if (!$user) {
                return response()->json(['message' => 'المستخدم غير موجود مسبقاً.'], 404);
            }

            if ($request->user() && $request->user()->id == $user->id) {
                return response()->json(['message' => 'لا يمكنك حذف حسابك الشخصي المسجل به حالياً.'], 422);
            }

            DB::transaction(function () use ($user, $id) {
                $supabaseUrl = config('filesystems.disks.supabase.url') ?? env('SUPABASE_URL') ?? '';
                $baseUrl = rtrim($supabaseUrl, '/');

                // 1. حذف صور ومرفقات طلبات النزول الميداني من Supabase وقاعدة البيانات
                if (DB::getSchemaBuilder()->hasTable('field_visits')) {
                    $visitIds = DB::table('field_visits')->where('user_id', $id)->pluck('id');

                    if ($visitIds->isNotEmpty() && DB::getSchemaBuilder()->hasTable('field_visit_attachments')) {
                        $attachments = DB::table('field_visit_attachments')
                            ->whereIn('field_visit_id', $visitIds)
                            ->get();

                        foreach ($attachments as $att) {
                            $filePath = $att->file_path ?? $att->image_path ?? $att->url ?? $att->path ?? null;

                            if (!empty($filePath)) {
                                try {
                                    $cleanPath = str_replace($baseUrl . '/', '', $filePath);
                                    $cleanPath = ltrim($cleanPath, '/');
                                    Storage::disk('supabase')->delete($cleanPath);
                                } catch (\Throwable $e) {
                                    Log::warning("فشل حذف مرفق النزول الميداني رقم {$att->id}: " . $e->getMessage());
                                }
                            }
                        }

                        DB::table('field_visit_attachments')->whereIn('field_visit_id', $visitIds)->delete();
                    }

                    DB::table('field_visits')->where('user_id', $id)->delete();
                }

                // 2. حذف دراسات الجدوى وملفاتها (PDF وصور الغلاف) من Supabase بنفس الآلية
                if (DB::getSchemaBuilder()->hasTable('feasibility_studies')) {
                    $studies = DB::table('feasibility_studies')->where('user_id', $id)->get();

                    foreach ($studies as $study) {
                        // حذف ملف الـ PDF
                        if (!empty($study->pdf_file)) {
                            try {
                                $cleanPdfPath = str_replace($baseUrl . '/', '', $study->pdf_file);
                                $cleanPdfPath = ltrim($cleanPdfPath, '/');
                                Storage::disk('supabase')->delete($cleanPdfPath);
                            } catch (\Throwable $e) {
                                Log::warning("فشل حذف ملف PDF لدراسة الجدوى رقم {$study->id}: " . $e->getMessage());
                            }
                        }

                        // حذف صورة الغلاف
                        if (!empty($study->cover_image)) {
                            try {
                                $cleanImagePath = str_replace($baseUrl . '/', '', $study->cover_image);
                                $cleanImagePath = ltrim($cleanImagePath, '/');
                                Storage::disk('supabase')->delete($cleanImagePath);
                            } catch (\Throwable $e) {
                                Log::warning("فشل حذف صورة الغلاف لدراسة الجدوى رقم {$study->id}: " . $e->getMessage());
                            }
                        }
                    }

                    DB::table('feasibility_studies')->where('user_id', $id)->delete();
                }

                if (DB::getSchemaBuilder()->hasTable('feasibility_requests')) {
                    DB::table('feasibility_requests')->where('user_id', $id)->delete();
                }

                // 3. حذف تقارير الزيارات إن كان المستخدم مهندساً
                if (DB::getSchemaBuilder()->hasTable('field_visit_reports')) {
                    DB::table('field_visit_reports')->where('engineer_id', $id)->delete();
                }

                // 4. حذف الإشعارات التابعة للمستخدم
                if (DB::getSchemaBuilder()->hasTable('notifications')) {
                    DB::table('notifications')->where(function ($q) use ($id) {
                        if (DB::getSchemaBuilder()->hasColumn('notifications', 'notifiable_id')) {
                            $q->where('notifiable_id', $id);
                        }
                        if (DB::getSchemaBuilder()->hasColumn('notifications', 'user_id')) {
                            $q->orWhere('user_id', $id);
                        }
                    })->delete();
                }

                // 5. حذف الاستشارات والمسودات
                if (DB::getSchemaBuilder()->hasTable('notifications')) {
                    DB::table('notifications')
                        ->where('notifiable_type', User::class)
                        ->where('notifiable_id', $id)
                        ->delete();
                }

                if (DB::getSchemaBuilder()->hasTable('drafts')) {
                    if (DB::getSchemaBuilder()->hasColumn('drafts', 'user_id')) {
                        DB::table('drafts')->where('user_id', $id)->delete();
                    }
                }

                // 6. حذف ملف المهندس وفك الصلاحيات
                if (DB::getSchemaBuilder()->hasTable('engineer_profiles')) {
                    DB::table('engineer_profiles')->where('user_id', $id)->delete();
                }
                if (method_exists($user, 'syncRoles')) {
                    $user->syncRoles([]);
                }

                // 7. الحذف النهائي لسجل المستخدم من جدول users
                DB::table('users')->where('id', $id)->delete();
            });

            return response()->json([
                'message' => 'تم حذف المستخدم وكافة ملفاته وصوره من Supabase وسجلاته بنجاح.'
            ], 200);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'تعذر الحذف لوجود قيود في قاعدة البيانات: ' . $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ غير متوقع أثناء عملية الحذف: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->validated()['password']),
        ]);

        return response()->json([
            'message' => 'تم تحديث كلمة المرور بنجاح'
        ], 200);
    }
}
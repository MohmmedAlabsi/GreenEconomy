<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['role', 'roles', 'region', 'specialization'])->paginate(15);
        return response()->json($users);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with(['role', 'roles', 'region', 'specialization', 'preferences', 'consultations'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'nullable|string|max:20|unique:users,phone',
            'email'             => 'nullable|email|max:255|unique:users,email',
            'password'          => 'required|string|min:8',
            'avatar'            => 'nullable|string|max:255',
            'governorate'       => 'nullable|string|max:100',
            'district'          => 'nullable|string|max:100',
            'membership_tier'   => 'nullable|string|max:50',
            'status'            => 'nullable|string|max:50',
            'identity_verified' => 'nullable|boolean',
            'role_id'           => 'nullable|exists:roles,id',
            'region_id'         => 'nullable|exists:regions,id',
            'specialization_id' => 'nullable|exists:specializations,id',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        // 1. إنشاء المستخدم في قاعدة البيانات
        $user = User::create($validated);

        // 2. 👈 إسناد دور Spatie تلقائياً إذا تم إرسال role_id
        if (!empty($validated['role_id'])) {
            $role = Role::findById($validated['role_id'], 'api');
            if ($role) {
                $user->assignRole($role);
            }
        }

        return response()->json([
            'message' => 'User created successfully',
            'data'    => $user->load(['role', 'roles'])
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'              => 'sometimes|string|max:255',
            'phone'             => 'nullable|string|max:20|unique:users,phone,' . $id,
            'email'             => 'nullable|email|max:255|unique:users,email,' . $id,
            'password'          => 'nullable|string|min:8',
            'avatar'            => 'nullable|string|max:255',
            'district'          => 'nullable|string|max:100',
            'membership_tier'   => 'nullable|string|max:50',
            'status'            => 'nullable|string|max:50',
            'identity_verified' => 'nullable|boolean',
            'role_id'           => 'nullable|exists:roles,id',
            'region_id'         => 'nullable|exists:regions,id',
            'specialization_id' => 'nullable|exists:specializations,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        // 3. 👈 تحديث دور Spatie مع حذف الدور القديم واستبداله بـ syncRoles
        if (array_key_exists('role_id', $validated)) {
            if ($validated['role_id']) {
                $role = Role::findById($validated['role_id'], 'api');
                if ($role) {
                    $user->syncRoles([$role]);
                }
            } else {
                $user->syncRoles([]); // إلغاء الدور إذا تم إرسال role_id كـ null
            }
        }

        return response()->json([
            'message' => 'User updated successfully',
            'data'    => $user->load(['role', 'roles'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
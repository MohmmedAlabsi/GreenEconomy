<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['region', 'role']);

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

        $users = $query->latest()->paginate(10);

        return response()->json($users);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with([
            'role', 
            'roles', 
            'region', 
            'engineerProfile.specialization', 
            'preferences', 
            'consultations'
        ])->findOrFail($id);

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
            'district'          => 'nullable|string|max:100',
            'membership_tier'   => 'nullable|string|max:50',
            'status'            => 'nullable|string|max:50',
            'identity_verified' => 'nullable|boolean',
            'role_id'           => 'nullable|exists:roles,id',
            'region_id'         => 'nullable|exists:regions,id',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        // البحث عن الدور بـ ID المباشر لتجنب تعارض الـ guard
        if (!empty($validated['role_id'])) {
            $role = Role::find($validated['role_id']);
            if ($role) {
                $user->assignRole($role);
            }
        }

        return response()->json([
            'message' => 'User created successfully',
            'data'    => $user->load(['role', 'roles', 'region', 'engineerProfile.specialization'])
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
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        if (array_key_exists('role_id', $validated)) {
            if ($validated['role_id']) {
                $role = Role::find($validated['role_id']);
                if ($role) {
                    $user->syncRoles([$role]);
                }
            } else {
                $user->syncRoles([]);
            }
        }

        return response()->json([
            'message' => 'User updated successfully',
            'data'    => $user->load(['role', 'roles', 'region', 'engineerProfile.specialization'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
    try {
        $user = User::findOrFail($id);

        // 1. منع المستخدم من حذف نفسه
        if ($request->user() && $request->user()->id == $user->id) {
            return response()->json([
                'message' => 'لا يمكنك حذف حسابك الشخصي المسجل به حالياً.'
            ], 422);
        }

        // 2. حذف/فك ارتباط العلاقات المباشرة لتفادي تعارض Foreign Key
        if (method_exists($user, 'consultations')) {
            $user->consultations()->delete();
        }
        if (method_exists($user, 'engineerProfile')) {
            $user->engineerProfile()->delete();
        }
        if (method_exists($user, 'preferences')) {
            $user->preferences()->delete();
        }
        
        // فك ارتباط الأدوار (Spatie Permissions)
        $user->syncRoles([]);

        // 3. تنفيذ الحذف النهائي
        $user->delete();

        return response()->json([
            'message' => 'تم حذف المستخدم وكافة سجلاته المرتبطة بنجاح.'
        ], 200);

    } catch (QueryException $e) {
        return response()->json([
            'message' => 'تعذر الحذف لوجود سجلات أخرى مرتبطة لا يمكن إزالتها تلقائياً.'
        ], 400);
    }
    }
}

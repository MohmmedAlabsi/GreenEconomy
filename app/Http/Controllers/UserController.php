<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Database\QueryException;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['region', 'role']); //[cite: 31]

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%");
            }); //[cite: 31]
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status')); //[cite: 31]
        }
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id')); //[cite: 31]
        }
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->input('region_id')); //[cite: 31]
        }

        $users = $query->latest()->paginate(10); //[cite: 31]
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with([
            'role', 
            'roles', 
            'region', 
            'engineerProfile.specialization', 
            'preferences', 
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
            $user = User::findOrFail($id); //[cite: 31]

            if ($request->user() && $request->user()->id == $user->id) {
                return response()->json([
                    'message' => 'لا يمكنك حذف حسابك الشخصي المسجل به حالياً.'
                ], 422); //[cite: 31]
            }

            if (method_exists($user, 'consultations')) {
                $user->consultations()->delete(); //[cite: 31]
            }
            if (method_exists($user, 'engineerProfile')) {
                $user->engineerProfile()->delete(); //[cite: 31]
            }
            if (method_exists($user, 'preferences')) {
                $user->preferences()->delete(); //[cite: 31]
            }
            
            $user->syncRoles([]); //[cite: 31]
            $user->delete(); //[cite: 31]

            return response()->json([
                'message' => 'تم حذف المستخدم وكافة سجلاته المرتبطة بنجاح.'
            ], 200); //[cite: 31]

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'تعذر الحذف لوجود سجلات أخرى مرتبطة لا يمكن إزالتها تلقائياً.'
            ], 400); //[cite: 31]
        }
    }
}
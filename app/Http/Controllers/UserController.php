<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
      // عرض قائمة المستخدمين مع التصفية حسب الدور
    public function index()
    {
        $users = User::with(['role', 'region', 'specialization'])->paginate(15);
        return response()->json($users);
    }

    // عرض ملف مستخدم محدد مع تفضيلاته واستشاراته
    public function show($id)
    {
        $user_id = User::with('role', 'region', 'specialization', 'preference', 'consultation') -> findOrFail($id);
        return response()->json($user_id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
 * Store a newly created resource.
 */
public function store(Request $request)
{
    $validated = $request->validate([

        'name' => 'required|string|max:255',

        'phone' => 'nullable|string|max:20|unique:users,phone',

        'email' => 'nullable|email|max:255|unique:users,email',

        'password' => 'required|string|min:8',

        'avatar' => 'nullable|string|max:255',

        'governorate' => 'nullable|string|max:100',

        'district' => 'nullable|string|max:100',

        'crop_types' => 'nullable|string|max:255',

        'membership_tier' => 'nullable|string|max:50',

        'status' => 'nullable|string|max:50',

        'identity_verified' => 'nullable|boolean',

        'role_id' => 'nullable|exists:roles,id',

        'region_id' => 'nullable|exists:regions,id',

        'specialization_id' => 'nullable|exists:specializations,id',

    ]);

    $validated['password'] = bcrypt($validated['password']);

    $user = User::create($validated);

    return response()->json([

        'message' => 'User created successfully',

        'data' => $user

    ],201);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    /**
 * Update the specified resource.
 */
public function update(Request $request, string $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([

        'name' => 'sometimes|string|max:255',

        'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,

        'email' => 'nullable|email|max:255|unique:users,email,' . $id,

        'password' => 'nullable|string|min:8',

        'avatar' => 'nullable|string|max:255',

        'governorate' => 'nullable|string|max:100',

        'district' => 'nullable|string|max:100',

        'crop_types' => 'nullable|string|max:255',

        'membership_tier' => 'nullable|string|max:50',

        'status' => 'nullable|string|max:50',

        'identity_verified' => 'nullable|boolean',

        'role_id' => 'nullable|exists:roles,id',

        'region_id' => 'nullable|exists:regions,id',

        'specialization_id' => 'nullable|exists:specializations,id',

    ]);

    if (isset($validated['password'])) {
        $validated['password'] = bcrypt($validated['password']);
    }

    $user->update($validated);

    return response()->json([

        'message' => 'User updated successfully',

        'data' => $user

    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    /**
 * Remove the specified resource.
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

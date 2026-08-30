<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles); //[cite: 29]
    }

    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();
        
        $role = Role::create([
            'name'       => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'api', //[cite: 29]
        ]);

        return response()->json([
            'message' => 'Role created successfully',
            'data'    => $role
        ], 201); //[cite: 29]
    }

    public function show($id)
    {
        $role = Role::findOrFail($id); //[cite: 29]
        return response()->json($role);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id); //[cite: 29]
        $role->update($request->validated()); //[cite: 29]

        return response()->json([
            'message' => 'Role updated successfully',
            'data'    => $role
        ]); //[cite: 29]
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id); //[cite: 29]
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]); //[cite: 29]
    }
}
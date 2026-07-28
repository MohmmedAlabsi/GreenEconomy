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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

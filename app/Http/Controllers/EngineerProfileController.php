<?php

namespace App\Http\Controllers;

use App\Models;
use Illuminate\Http\Request;
use App\Models\EngineerProfile;
use Illuminate\Routing\Controller;

class EngineerProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profiles = EngineerProfile::with(['user', 'specialization'])->paginate(15);
        return response()->json($profiles);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $profile = EngineerProfile::with(['user', 'specialization'])->findOrFail($id);
        return response()->json($profile);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'               => 'required|exists:users,id|unique:engineer_profiles,user_id',
            'specialization_id'     => 'nullable|exists:specializations,id',
            'years_of_experience'   => 'nullable|integer|min:0',
            'bio'                   => 'nullable|string',
            'cv_file'               => 'nullable|string|max:255',
        ]);

        $profile = EngineerProfile::create($validated);

        return response()->json([
            'message' => 'Engineer profile created successfully',
            'data'    => $profile->load(['user', 'specialization'])
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $profile = EngineerProfile::findOrFail($id);

        $validated = $request->validate([
            'specialization_id'     => 'nullable|exists:specializations,id',
            'years_of_experience'   => 'nullable|integer|min:0',
            'bio'                   => 'nullable|string',
            'cv_file'               => 'nullable|string|max:255',
        ]);

        $profile->update($validated);

        return response()->json([
            'message' => 'Engineer profile updated successfully',
            'data'    => $profile->load(['user', 'specialization'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $profile = EngineerProfile::findOrFail($id);
        $profile->delete();

        return response()->json([
            'message' => 'Engineer profile deleted successfully'
        ]);
    }
}
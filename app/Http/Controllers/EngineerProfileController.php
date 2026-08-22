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
public function show(Request $request, $id = null)
    {
        // إذا لم يتم تمرير id في الرابط، جلب الملف الخاص بالمستخدم المسجل حالياً
        if (!$id) {
            $profile = EngineerProfile::with(['user', 'specialization'])
                ->where('user_id', $request->user()->id)
                ->first();
                
            if (!$profile) {
                return response()->json(['data' => null], 200);
            }
            
            return response()->json($profile);
        }

        $profile = EngineerProfile::with(['user', 'specialization'])->findOrFail($id);
        return response()->json($profile);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'specialization_id'     => 'nullable|exists:specializations,id',
            'years_of_experience'   => 'nullable|integer|min:0',
            'qualification'         => 'nullable|string|max:255',
            'bio'                   => 'nullable|string',
            // تعديل قاعدة التحقق لتسمح بملف PDF أو نص (في حال لم يتم رفع ملف جديد وبقي القديم)
            'cv_file'               => 'nullable|sometimes|file|mimes:pdf|max:5120', 
        ]);

        $profileData = [
            'specialization_id'     => $request->specialization_id,
            'years_of_experience'   => $request->years_of_experience,
            'qualification'         => $request->qualification,
            'bio'                   => $request->bio,
        ];

        // معالجة رفع الملف الجديد
        if ($request->hasFile('cv_file')) {
            $profileData['cv_file'] = $request->file('cv_file')->store('cv_files', 'public');
        }

        $profile = EngineerProfile::updateOrCreate(
            ['user_id' => $userId],
            $profileData
        );

        return response()->json([
            'message' => 'Profile saved successfully',
            'data'    => $profile->load(['user', 'specialization'])
        ], 200);
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
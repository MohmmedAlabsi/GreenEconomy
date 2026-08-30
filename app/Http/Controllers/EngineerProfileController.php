<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EngineerProfile;
use App\Http\Requests\StoreEngineerProfileRequest;
use App\Http\Requests\UpdateEngineerProfileRequest;

class EngineerProfileController extends Controller
{
    public function index()
    {
        $profiles = EngineerProfile::with(['user', 'specialization'])->paginate(15);
        return response()->json($profiles);
    }

    public function show(Request $request, $id = null)
    {
        if (!$id) {
            $profile = EngineerProfile::with(['user', 'specialization'])
                ->where('user_id', $request->user()->id)
                ->first();
                
            return response()->json($profile ? $profile : ['data' => null], 200);
        }

        $profile = EngineerProfile::with(['user', 'specialization'])->findOrFail($id);
        return response()->json($profile);
    }

    public function store(StoreEngineerProfileRequest $request)
    {
        $userId = $request->user()->id;
        $user = User::find($userId); 
        $validated = $request->validated();

        $profileData = [
            'specialization_id'     => $validated['specialization_id'] ?? null,
            'years_of_experience'   => $validated['years_of_experience'] ?? null,
            'qualification'         => $validated['qualification'] ?? null,
            'bio'                   => $validated['bio'] ?? null,
        ];

        if ($request->hasFile('cv_file')) {
            $profileData['cv_file'] = $request->file('cv_file')->store('cv_files', 'public');
        }
        
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = asset('storage/' . $avatarPath);
            $user->save();
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

    public function update(UpdateEngineerProfileRequest $request, string $id)
    {
        $profile = EngineerProfile::findOrFail($id);
        $profile->update($request->validated());

        return response()->json([
            'message' => 'Engineer profile updated successfully',
            'data'    => $profile->load(['user', 'specialization'])
        ]);
    }

    public function destroy(string $id)
    {
        $profile = EngineerProfile::findOrFail($id);
        $profile->delete();

        return response()->json(['message' => 'Engineer profile deleted successfully']);
    }
}
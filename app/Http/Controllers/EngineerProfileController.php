<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\EngineerProfile;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreEngineerProfileRequest;
use App\Http\Requests\UpdateEngineerProfileRequest;

class EngineerProfileController extends Controller
{

    public function index(Request $request)
    {
        $profiles = EngineerProfile::with(['user.region', 'specialization'])->get();
        
        $engineers = $profiles->map(function ($profile) {
            // جلب جميع التقييمات المرتبطة بهذا المهندس من جدول الزيارات الميدانية
            $visitsWithRatings = \App\Models\FieldVisit::where('engineer_id', $profile->user_id)
                ->whereNotNull('rating')
                ->get();

            $reviewsCount = $visitsWithRatings->count();
            $averageRating = $reviewsCount > 0 ? round($visitsWithRatings->avg('rating'), 1) : 5;

            return [
                'id' => $profile->user_id,
                'name' => $profile->user->name ?? 'مهندس بدون اسم',
                'email' => $profile->user->email ?? '',
                'governorate' => $profile->governorate ?? $profile->user->governorate ?? '',
                'specialization_id' => $profile->specialization_id,
                'specialization' => $profile->specialization, // جلب التخصص الحقيقي المرتبط بـ specialization_id
                'engineerProfile' => $profile,
                'average_rating' => $averageRating,
                'reviews_count' => $reviewsCount,
            ];
        });

        return response()->json(['data' => $engineers]);
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

        // جلب الرابط الأساسي من الإعدادات
        $baseUrl = rtrim(config('filesystems.disks.supabase.url'), '/');

        // رفع ملف الـ CV وتوليد الرابط
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('cv_files', 'supabase');
            $profileData['cv_file'] = $baseUrl . '/' . $cvPath;
        }
        
        // رفع الصورة الشخصية وتوليد الرابط
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'supabase');
            $user->avatar = $baseUrl . '/' . $avatarPath;
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
        $user = User::find($profile->user_id);
        $validated = $request->validated();
        
        $baseUrl = rtrim(config('filesystems.disks.supabase.url'), '/');

        // تحديث ملف CV مع حذف الملف القديم من السيرفر
        if ($request->hasFile('cv_file')) {
            if ($profile->cv_file && str_contains($profile->cv_file, 'supabase.co')) {
                $oldCvPath = preg_replace('/^.*\/cv_files\//', 'cv_files/', $profile->cv_file);
                Storage::disk('supabase')->delete($oldCvPath);
            }
            $cvPath = $request->file('cv_file')->store('cv_files', 'supabase');
            $validated['cv_file'] = $baseUrl . '/' . $cvPath;
        }

        // تحديث الصورة الشخصية مع حذف الصورة القديمة
        if ($request->hasFile('avatar')) {
            if ($user->avatar && str_contains($user->avatar, 'supabase.co')) {
                $oldAvatarPath = preg_replace('/^.*\/avatars\//', 'avatars/', $user->avatar);
                Storage::disk('supabase')->delete($oldAvatarPath);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'supabase');
            $user->avatar = $baseUrl . '/' . $avatarPath;
            $user->save();
        }

        $profile->update($validated);

        return response()->json([
            'message' => 'Engineer profile updated successfully',
            'data'    => $profile->load(['user', 'specialization'])
        ]);
    }

    public function destroy(string $id)
    {
        $profile = EngineerProfile::findOrFail($id);
        
        // حذف ملف الـ CV من Supabase قبل مسح السجل
        if ($profile->cv_file && str_contains($profile->cv_file, 'supabase.co')) {
            $oldCvPath = preg_replace('/^.*\/cv_files\//', 'cv_files/', $profile->cv_file);
            Storage::disk('supabase')->delete($oldCvPath);
        }
        
        $profile->delete();

        return response()->json(['message' => 'Engineer profile deleted successfully']);
    }
}
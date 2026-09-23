<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\EngineerProfile;

class EngineerProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جلب أول 10 مستخدمين ليس لديهم بروفايل مهندس بعد
        // (query on engineer_profiles.user_id directly so this does not depend on a model relation)
        $profiledUserIds = EngineerProfile::query()->pluck('user_id');

        $usersWithoutProfile = User::query()
            ->whereNotIn('id', $profiledUserIds)
            ->orderBy('id')
            ->take(10)
            ->get();

        foreach ($usersWithoutProfile as $user) {
            EngineerProfile::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}

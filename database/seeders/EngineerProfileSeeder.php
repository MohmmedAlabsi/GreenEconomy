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
        $usersWithoutProfile = User::doesntHave('engineerProfile')->take(10)->get();

        foreach ($usersWithoutProfile as $user) {
            EngineerProfile::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
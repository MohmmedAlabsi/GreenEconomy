<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Seeder;

class UserPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function ($user) {

            UserPreference::factory()->create([
                'user_id' => $user->id,
            ]);

        });
    }
}
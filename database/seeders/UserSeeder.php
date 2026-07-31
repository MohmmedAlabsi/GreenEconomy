<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Role;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم مدير للنظام

        User::create([

            'name' => 'System Administrator',

            'phone' => '770000000',

            'email' => 'admin@greeneconomy.com',

            'password' => bcrypt('password'),
            'avatar' => 'https://www.gravatar.com/avatar/' . md5('admin@greeneconomy.com'),

            'governorate' => 'صنعاء',

            'district' => 'التحرير',

            'crop_types' => 'Coffee, Wheat, Vegetables',

            'membership_tier' => 'premium',

            'status' => 'active',

            'identity_verified' => true,

            'role_id' => Role::query()->first()?->id,

            'region_id' => Region::query()->first()?->id,

            'specialization_id' => Specialization::query()->first()?->id,
        ]);

        // إنشاء 50 مستخدم تجريبي

        User::factory(50)->create();
    }
}
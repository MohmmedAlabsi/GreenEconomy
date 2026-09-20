<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم مدير للنظام
        $adminPassword = env('ADMIN_DEFAULT_PASSWORD');
        if (!$adminPassword) {
            $adminPassword = Str::random(16);
            $this->command?->warn("تم توليد كلمة مرور عشوائية لحساب الأدمن: {$adminPassword} — يرجى حفظها وتغييرها فوراً بعد أول تسجيل دخول.");
        }

        User::firstOrCreate([

            ['email' => 'admin@greeneconomy.com'],
            'phone' => '770000000',
            'name' => 'System Administrator',
            'password' => bcrypt($adminPassword),
            'avatar' => 'https://www.gravatar.com/avatar/' . md5('admin@greeneconomy.com'),
            'district' => 'التحرير',
            'membership_tier' => 'premium',
            'status' => 'active',
            'identity_verified' => true,
            'role_id' => Role::where('name', 'Admin')->first()?->id,
            'region_id' => Region::query()->first()?->id,
        ]);

        // إنشاء 50 مستخدم تجريبي

        User::factory(50)->create();
    }
}
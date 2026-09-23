<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        // استخدام updateOrCreate لضمان وجود سجل الإعدادات وتفادي تكراره
        PlatformSetting::updateOrCreate(
            ['id' => 1],
            [
                'platform_name'    => 'منصة الاقتصاد الأخضر',
                'support_email'    => 'support@greeneconomy.ye',
                'support_phone'    => '+967770000000',
                'maintenance_mode' => false,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]
        );
    }
}
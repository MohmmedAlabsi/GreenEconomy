<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        PlatformSetting::insert([
            [
                'platform_name'      => 'Green Economy Platform',
                'default_language'   => 'en',
                'timezone'           => 'Asia/Aden',
                'date_format'        => 'DD/MM/YYYY',
                'production_api_key' => null,
                'test_api_key'       => null,
                'updated_at'         => now(),
            ]
        ]);
    }
}
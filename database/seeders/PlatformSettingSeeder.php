<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Support\Str;
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
                'production_api_key' => 'prod_' . Str::random(32),
                'test_api_key'       => 'test_' . Str::random(32),
                'updated_at'         => now(),
            ]
        ]);
    }
}
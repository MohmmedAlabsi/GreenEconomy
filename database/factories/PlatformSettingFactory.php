<?php

namespace Database\Factories;

use App\Models\PlatformSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformSettingFactory extends Factory
{
    protected $model = PlatformSetting::class;

    public function definition(): array
    {
        return [
            'platform_name' => 'Green Economy Platform',
            'default_language' => 'ar',
            'timezone' => 'Asia/Aden',
            'date_format' => 'DD/MM/YYYY',
            'production_api_key' => fake()->sha256(),
            'test_api_key' => fake()->sha256(),
            'updated_at' => now(),
        ];
    }
}
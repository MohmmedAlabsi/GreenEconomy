<?php

namespace Database\Factories;

use App\Models\PlatformSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformSettingFactory extends Factory
{
    protected $model = PlatformSetting::class;

    /**
     * Matches the platform_settings migration exactly:
     * platform_name, support_email, support_phone, maintenance_mode, timestamps.
     */
    public function definition(): array
    {
        return [
            'platform_name'    => 'منصة الاقتصاد الأخضر',
            'support_email'    => 'support@greeneconomy.ye',
            'support_phone'    => '+967' . '77' . fake()->numerify('#######'),
            'maintenance_mode' => false,
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
    }
}

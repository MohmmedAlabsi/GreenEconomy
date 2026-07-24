<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        Region::insert([
            ['name' => 'صنعاء', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'عدن', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'تعز', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الحديدة', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'إب', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ذمار', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'حضرموت', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'شبوة', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مأرب', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'حجة', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'صعدة', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'المحويت', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ريمة', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الجوف', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'المهرة', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'أبين', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'لحج', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'الضالع', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'البيضاء', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'عمران', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'سقطرى', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'أمانة العاصمة', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
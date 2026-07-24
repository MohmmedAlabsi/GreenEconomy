<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Administrator' => [
                'System Administration',
                'User Management',
            ],

            'Farmer' => [
                'Crop Farming',
                'Livestock',
            ],

            'Agricultural Expert' => [
                'Plant Diseases',
                'Soil & Irrigation',
            ],

            'Industrial Investor' => [
                'Food Industries',
                'Renewable Energy',
            ],

            'Researcher' => [
                'Environmental Research',
                'Sustainability',
            ],

            'Government Officer' => [
                'Environmental Regulation',
                'Project Evaluation',
            ],

            'Content Manager' => [
                'Knowledge Base',
                'Educational Content',
            ],

            'Technical Support' => [
                'Platform Support',
                'System Maintenance',
            ],
        ];

        foreach ($data as $roleName => $specializations) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                continue;
            }

            foreach ($specializations as $name) {
                Specialization::create([
                    'role_id' => $role->id,
                    'name' => $name,
                ]);
            }
        }
    }
}
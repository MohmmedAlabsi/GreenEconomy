<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            CategorySeeder::class,
            RoleSeeder::class,
            PlatformSettingSeeder::class,
            RegionSeeder::class,
            SpecializationSeeder::class,
            UserSeeder::class,
            UserPreferenceSeeder::class,
            ConsultationSeeder::class,
            ConsultationRequestSeeder::class,
            PlantDiseaseSeeder::class,
            DiseaseTreatmentSeeder::class,
            FeasibilityStudySeeder::class,
            FeasibilityRequestSeeder::class,
            FieldVisitSeeder::class,
            KnowledgeBaseItemSeeder::class,
            PlantSeeder::class,
            AttachmentSeeder::class,
            ActivityLogSeeder::class,
            ConsultationRequestSeeder::class,
            PlantDiseaseSeeder::class,
            DiseaseTreatmentSeeder::class,
            FeasibilityStudySeeder::class,
            FeasibilityRequestSeeder::class,
            FieldVisitSeeder::class,
            KnowledgeBaseItemSeeder::class,
            PlantSeeder::class,
            
            
            


        ]);

        
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

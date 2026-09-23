<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Order matters (parents before children):
     *   Category / Role / PlatformSetting / Region   -> no dependencies
     *   Specialization                               -> roles
     *   User                                         -> roles, regions
     *   everything after UserSeeder                  -> users (+ categories / regions / plant_diseases)
     */
    public function run(): void
    {
        // The seeders write through models (create / firstOrCreate / updateOrCreate);
        // un-guard for the duration of the run so a model's $fillable can never block seeding.
        Model::unguard();

        try {
            $this->call([
                // ---- reference data -------------------------------------------------
                CategorySeeder::class,
                RoleSeeder::class,
                PermissionSeeder::class,
                PlatformSettingSeeder::class,
                RegionSeeder::class,
                SpecializationSeeder::class,   // needs roles

                // ---- users (needs roles + regions) ----------------------------------
                UserSeeder::class,

                // ---- domain data (needs users / categories / regions) ---------------
                ConsultationSeeder::class,
                ConsultationRequestSeeder::class,
                PlantDiseaseSeeder::class,
                DiseaseTreatmentSeeder::class, // needs plant_diseases
                FeasibilityStudySeeder::class,
                FeasibilityRequestSeeder::class,
                FieldVisitSeeder::class,
                KnowledgeBaseItemSeeder::class,
                PlantSeeder::class,

                // ---- polymorphic / dependent data -----------------------------------
                AttachmentSeeder::class,       // needs consultations, feasibility studies, plant diseases
                ActivityLogSeeder::class,
                EngineerProfileSeeder::class,  // needs users + specializations
            ]);
        } finally {
            Model::reguard();
        }
    }
}

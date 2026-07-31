<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\FeasibilityStudy;
use App\Models\PlantDisease;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Attachment;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        Consultation::all()->each(function ($consultation) use ($users) {
            Attachment::create([
                'attachable_type' => Consultation::class,
                'attachable_id' => $consultation->id,
                'user_id' => $consultation->user_id,
                'file_name' => 'consultation_' . $consultation->id . '.pdf',
                'file_path' => 'attachments/consultation_' . $consultation->id . '.pdf',
                'file_type' => 'pdf',
                'file_size' => 512000,
                'url' => '/storage/attachments/consultation_' . $consultation->id . '.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        FeasibilityStudy::all()->each(function ($study) {
            Attachment::create([
                'attachable_type' => FeasibilityStudy::class,
                'attachable_id' => $study->id,
                'user_id' => $study->user_id,
                'file_name' => 'feasibility_' . $study->id . '.pdf',
                'file_path' => 'attachments/feasibility_' . $study->id . '.pdf',
                'file_type' => 'pdf',
                'file_size' => 1024000,
                'url' => '/storage/attachments/feasibility_' . $study->id . '.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        PlantDisease::all()->each(function ($disease) use ($users) {
            $user = $users->random();
            Attachment::create([
                'attachable_type' => PlantDisease::class,
                'attachable_id' => $disease->id,
                'user_id' => $user->id,
                'file_name' => 'disease_' . $disease->id . '.jpg',
                'file_path' => 'attachments/disease_' . $disease->id . '.jpg',
                'file_type' => 'jpg',
                'file_size' => 256000,
                'url' => '/storage/attachments/disease_' . $disease->id . '.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}

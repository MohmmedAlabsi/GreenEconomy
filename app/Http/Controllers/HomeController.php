<?php

namespace App\Http\Controllers;

use App\Models\FieldVisit;
use App\Models\FeasibilityRequest; // أو الجدول الخاص بدراسات الجدوى لديك
use App\Models\EngineerProfile;
use App\Models\PlantDisease;
use App\Models\Plant;
use App\Models\DiseaseTreatment;
use App\Models\FeasibilityStudy;

class HomeController extends Controller
{

        public function getStats() 
        {
            $fieldVisitsCount = FieldVisit::count();
            $feasibilityCount = FeasibilityStudy::count();
            
            $engineersCount = EngineerProfile::count();
            
            $diseasesCount = class_exists(PlantDisease::class) ? PlantDisease::count() : 0;
            $plantsCount = class_exists(Plant::class) ? Plant::count() : 0;
            $treatmentsCount = class_exists(DiseaseTreatment::class) ? DiseaseTreatment::count() : 0;

            return response()->json([
                'field_visits_and_feasibility' => $fieldVisitsCount + $feasibilityCount,
                'engineers_count' => $engineersCount,
                'knowledge_base_count' => $diseasesCount + $plantsCount + $treatmentsCount,
            ]);
        }
}
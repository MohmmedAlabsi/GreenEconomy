<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiseaseTreatment extends Model
{
    protected $fillable = ['plant_disease_id', 'treatment_name', 'type', 'dosage_instructions'];

    public function disease()
    {
        return $this->belongsTo(PlantDisease::class, 'plant_disease_id');
    }
}

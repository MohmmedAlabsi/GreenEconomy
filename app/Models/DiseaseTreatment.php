<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiseaseTreatment extends Model
{
    protected $fillable = [
        'disease_id',
        'treatment_type',
        'title',
        'instructions',
    ];

    public function disease()
    {
        return $this->belongsTo(PlantDisease::class, 'disease_id');
    }
}

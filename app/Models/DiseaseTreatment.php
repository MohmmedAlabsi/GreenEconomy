<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiseaseTreatment extends Model
{
    use HasFactory;
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

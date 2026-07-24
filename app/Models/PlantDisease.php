<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlantDisease extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'scientific_name',
        'type',
        'symptoms',
        'cause_description',
        'image_url',
    ];

    public function plants()
    {
        return $this->belongsToMany(Plant::class, 'plant_disease_pivot', 'disease_id', 'plant_id');
    }

    public function treatments()
    {
        return $this->hasMany(DiseaseTreatment::class, 'disease_id');
    }
}

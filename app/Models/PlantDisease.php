<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantDisease extends Model
{
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

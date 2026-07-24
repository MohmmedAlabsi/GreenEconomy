<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantDisease extends Model
{
     protected $fillable = ['name', 'scientific_name', 'symptoms', 'cause_type'];

    public function plants()
    {
        return $this->belongsToMany(Plant::class, 'plant_disease')
                    ->withPivot('severity')
                    ->withTimestamps();
    }

    public function treatments()
    {
        return $this->hasMany(DiseaseTreatment::class);
    }
}

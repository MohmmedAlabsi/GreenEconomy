<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
  protected $fillable = ['category_id', 'name', 'scientific_name', 'planting_season', 'water_requirements'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function diseases()
    {
        return $this->belongsToMany(PlantDisease::class, 'plant_disease')
                    ->withPivot('severity')
                    ->withTimestamps();
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}

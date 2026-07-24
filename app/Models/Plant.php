<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
  protected $fillable = [
        'common_name',
        'scientific_name',
        'description',
        'climate_requirements',
        'irrigation_schedule',
        'planting_season',
        'category_id',
        'image_url',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function diseases()
    {
        return $this->belongsToMany(PlantDisease::class, 'plant_disease_pivot', 'plant_id', 'disease_id');
    }
}

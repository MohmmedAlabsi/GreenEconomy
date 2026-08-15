<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlantDisease extends Model
{
    use HasFactory;

    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // تنظيف المسار لضمان عدم تكرار كلمة storage
        $cleanPath = ltrim(str_replace('storage/', '', $value), '/');

        return asset('storage/' . $cleanPath);
    }

    protected $fillable = [
        'name',
        'scientific_name',
        'plant_type',
        'type',
        'severity_level',
        'spread_rate',
        'farmer_visibility',
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
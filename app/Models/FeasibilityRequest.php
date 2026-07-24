<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeasibilityRequest extends Model
{
    use HasFactory;
      protected $fillable = [
        'user_id',
        'project_title',
        'category_id',
        'region_id',
        'estimated_budget',
        'land_area',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}

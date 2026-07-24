<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeasibilityStudy extends Model
{
    use HasFactory;
     protected $fillable = [
        'title',
        'description',
        'category_id',
        'region_id',
        'cover_image',
        'capital_required',
        'expected_roi',
        'payback_period',
        'risk_level',
        'status',
        'pdf_file',
        'user_id',
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

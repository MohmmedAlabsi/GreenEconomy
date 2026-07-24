<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeasibilityStudy extends Model
{
    protected $fillable = ['category_id', 'title', 'summary', 'estimated_cost', 'expected_roi', 'file_path'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
 
}

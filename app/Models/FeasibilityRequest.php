<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeasibilityRequest extends Model
{
    protected $fillable = ['user_id', 'project_title', 'budget_range', 'details', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

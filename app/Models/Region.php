<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
      protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function feasibilityStudies()
    {
        return $this->hasMany(FeasibilityStudy::class);
    }

    public function feasibilityRequests()
    {
        return $this->hasMany(FeasibilityRequest::class);
    }
}

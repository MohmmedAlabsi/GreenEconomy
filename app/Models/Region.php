<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
      protected $fillable = ['name', 'code', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function fieldVisits()
    {
        return $this->hasMany(FieldVisit::class);
    }
}

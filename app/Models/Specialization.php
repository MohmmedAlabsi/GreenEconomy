<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'name',
    ];


    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }
}
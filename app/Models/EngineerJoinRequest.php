<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineerJoinRequest extends Model
{
    use HasFactory;

    protected $table = 'engineer_join_requests';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'region_id',
        'district',
        'specialization_id',
        'qualification',
        'years_of_experience',
        'bio',
        'cv_file',
        'status',
        'notes',
    ];

    // علاقات اختياريّة تفيدك لاحقاً في العرض ضمن لوحة التحكم
    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
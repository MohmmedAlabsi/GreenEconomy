<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserPreference;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
   use HasApiTokens,HasRoles, Notifiable;
   use HasFactory;
   protected $guard_name = "api";

    protected $fillable = [
        'name',
        'phone',
        'email',
        'email_verified_at',
        'password',
        'avatar',
        'governorate',
        'district',
        'crop_types',
        'membership_tier',
        'status',
        'identity_verified',
        'role_id',
        'region_id',
        'specialization_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'identity_verified' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public function preference()
    {
        return $this->hasOne(UserPreference::class);
    }

    public function feasibilityStudies()
    {
        return $this->hasMany(FeasibilityStudy::class);
    }

    public function feasibilityRequests()
    {
        return $this->hasMany(FeasibilityRequest::class);
    }

    public function knowledgeBaseItems()
    {
        return $this->hasMany(KnowledgeBaseItem::class);
    }
    public function Consultation()
    {
        return $this->hasMany(Consultation::class);
    }
}
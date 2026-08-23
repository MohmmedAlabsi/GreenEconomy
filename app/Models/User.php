<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserPreference;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable, HasFactory , SoftDeletes;

    protected $guard_name = "api";

    protected $fillable = [
        'name',
        'phone',
        'email',
        'email_verified_at',
        'password',
        'avatar',
        'district',
        'membership_tier',
        'status',
        'identity_verified',
        'role_id',
        'region_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'identity_verified' => 'boolean',
    ];

    // العلاقة مع ملف المهندس (إضافة حديثة)
    public function engineerProfile()
    {
        return $this->hasOne(EngineerProfile::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function preferences()
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

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }
}
<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Database\Factories\UserFactory;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'role_id', 'region_id', 'specialization_id',
        'name', 'email', 'phone', 'password', 'status'
    ];

    protected $hidden = ['password', 'remember_token'];

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

    public function farmerConsultations()
    {
        return $this->hasMany(Consultation::class, 'farmer_id');
    }

    public function engineerConsultations()
    {
        return $this->hasMany(Consultation::class, 'engineer_id');
    }

    public function fieldVisitsAsFarmer()
    {
        return $this->hasMany(FieldVisit::class, 'farmer_id');
    }

    public function fieldVisitsAsEngineer()
    {
        return $this->hasMany(FieldVisit::class, 'engineer_id');
    }

    public function feasibilityRequests()
    {
        return $this->hasMany(FeasibilityRequest::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}

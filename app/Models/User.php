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

    /**
     * Scope a query to users whose role is "Admin".
     *
     * Extracted from the repeated `whereHas('role', ...)` lookups that used to
     * live in the field-visit / feasibility controllers.
     */
    public function scopeAdmins($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('role', fn ($role) => $role->whereIn('name', ['admin', 'Admin']))
                ->orWhereHas('roles', fn ($role) => $role->whereIn('name', ['admin', 'Admin']))
                ->orWhere('role_id', 1);
        });
    }

    public function roleName(): string
    {
        return strtolower((string) ($this->role?->name ?? $this->roles->first()?->name ?? match ($this->role_id) {
            1 => 'admin',
            2 => 'farmer',
            3 => 'engineer',
            default => '',
        }));
    }

    public function hasRole($roles, $guard = null): bool
    {
        foreach ((array) $roles as $role) {
            $name = is_object($role) ? $role->name : $role;
            if (strtolower((string) $name) === $this->roleName()) {
                return true;
            }
        }

        return $this->roles()->whereIn('name', array_map(
            fn ($role) => is_object($role) ? $role->name : $role,
            (array) $roles,
        ))->exists();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roleName() === 'admin' || $this->can($permission);
    }

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_notifications_enabled',
        'browser_notifications_enabled',
        'sms_critical_alerts_enabled',
        'two_factor_auth_enabled',
    ];

    protected function casts(): array
    {
        return [
            'email_notifications_enabled' => 'boolean',
            'browser_notifications_enabled' => 'boolean',
            'sms_critical_alerts_enabled' => 'boolean',
            'two_factor_auth_enabled' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
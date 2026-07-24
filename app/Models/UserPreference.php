<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
     protected $fillable = [
        'user_id',
        'email_notifications_enabled',
        'browser_notifications_enabled',
        'sms_critical_alerts_enabled',
        'two_factor_auth_enabled',
    ];

    protected $casts = [
        'email_notifications_enabled'   => 'boolean',
        'browser_notifications_enabled' => 'boolean',
        'sms_critical_alerts_enabled'   => 'boolean',
        'two_factor_auth_enabled'       => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationRequest extends Model
{
      protected $fillable = [
        'user_id',
        'assigned_consultant_id',
        'subject',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedConsultant()
    {
        return $this->belongsTo(User::class, 'assigned_consultant_id');
    }
}

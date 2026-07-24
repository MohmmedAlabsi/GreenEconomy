<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsultationRequest extends Model
{
    use HasFactory;
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

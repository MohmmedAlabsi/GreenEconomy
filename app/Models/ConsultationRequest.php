<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationRequest extends Model
{
      protected $fillable = ['user_id', 'consultation_type', 'scheduled_at', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

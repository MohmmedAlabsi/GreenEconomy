<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
  protected $fillable = [
        'user_id',
        'issue_title',
        'crop_type',
        'crop_age',
        'issue_duration',
        'description',
        'status',
        'assigned_expert_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedExpert()
    {
        return $this->belongsTo(User::class, 'assigned_expert_id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}

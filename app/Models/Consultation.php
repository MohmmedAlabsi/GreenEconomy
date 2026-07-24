<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consultation extends Model
{
    use HasFactory;
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

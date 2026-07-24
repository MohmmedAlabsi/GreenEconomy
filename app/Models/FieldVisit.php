<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldVisit extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'contact_name',
        'contact_phone',
        'governorate',
        'district',
        'village_or_area',
        'nearest_landmark',
        'crop_type',
        'area_size',
        'infestation_type',
        'priority_level',
        'problem_description',
        'status',
        'scheduled_at',
        'estimated_cost',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'area_size'      => 'decimal:2',
        'estimated_cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}

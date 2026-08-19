<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'engineer_id',
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
        'current_step',
        'scheduled_at',
        'estimated_cost',
        'rating',
        'rating_comment',
    ];

    protected $casts = [
        'scheduled_at'   => 'datetime',
        'area_size'      => 'decimal:2',
        'estimated_cost' => 'decimal:2',
        'current_step'   => 'integer',
        'rating'         => 'integer',
    ];

    // المزارع صاحب الطلب
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // المهندس الزراعي المكلف بالزيارة
    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    // التقرير الميداني الصادر للزيارة
    public function report()
    {
        return $this->hasOne(FieldVisitReport::class);
    }

    // المرفقات والصور الميدانية
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
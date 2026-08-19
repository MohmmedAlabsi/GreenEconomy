<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldVisitReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_visit_id',
        'engineer_id',
        'diagnosis',
        'recommendations',
        'prescribed_inputs',
        'notes',
    ];

    // الزيارة الميدانية المرتبطة بالتقرير
    public function fieldVisit()
    {
        return $this->belongsTo(FieldVisit::class, 'field_visit_id');
    }

    // المهندس صاحب التقرير
    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    // صور ومعاينات التقرير
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
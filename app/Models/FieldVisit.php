<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldVisit extends Model
{
    protected $fillable = ['farmer_id', 'engineer_id', 'region_id', 'visit_date', 'cost', 'report', 'status'];

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}

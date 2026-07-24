<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ActivityLog extends Model
{
    use HasFactory;
   public $timestamps = false; // الجدول يحتوي فقط على created_at بدون updated_at

    protected $fillable = [
        'user_id',
        'action_text',
        'target_type',
        'target_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    } 
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization_id',
        'years_of_experience',
        'qualification',
        'bio',
        'cv_file',
    ];

    /**
     * العلاقة مع جدول المستخدمين (BelongsTo)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع جدول التخصصات (BelongsTo)
     */
    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlantDisease extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',               // اسم المرض
        'scientific_name',    // الاسم العلمي
        'plant_type',         // نوع المحصول / النبات
        'type',               // التصنيف (فطري، بكتيري، حشري...)
        'severity_level',     // مستوى الخطورة (منخفض، متوسط، عالي)
        'spread_rate',        // سرعة انتشار المرض (بطيء، متوسط، سريع)
        'farmer_visibility',  // العرض للمزارعين (مرئي للمزارعين، مخفي)
        'symptoms',           // وصف تفصيلي للأعراض
        'image_url',          // مسار الصورة المحفوظة
    ];

    public function plants()
    {
        return $this->belongsToMany(Plant::class, 'plant_disease_pivot', 'disease_id', 'plant_id');
    }

    public function treatments()
    {
        return $this->hasMany(DiseaseTreatment::class, 'disease_id');
    }

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // تأكد من ضبط APP_URL في ملف .env بشكل صحيح
            return asset('storage/' . $this->image);
        }
        return null;
    }
}

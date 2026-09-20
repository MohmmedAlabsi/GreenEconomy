<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlatformSetting extends Model
{
    use HasFactory;
    public $timestamps = false; // لا يحتوي الجدول على created_at

    protected $fillable = [
        'platform_name',
        'support_email',
        'support_phone',
        'maintenance_mode',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
    ];

    public function index()
    {
        $settings = PlatformSetting::firstOrCreate([], [
            'platform_name'    => 'منصة الاقتصاد الأخضر',
            'support_email'    => 'support@greeneconomy.ye',
            'support_phone'    => '770000000',
            'maintenance_mode' => false,
        ]);

        return response()->json(['status' => true, 'message' => 'تم جلب إعدادات المنصة بنجاح', 'data' => $settings]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PlatformSetting;
use App\Http\Requests\UpdatePlatformSettingRequest;
use Illuminate\Routing\Controller;

class PlatformSettingController extends Controller

{
    /**
     * جلب إعدادات المنصة الحالية
     */
    public function index()
    {
        $settings = PlatformSetting::firstOrCreate(
            ['id' => 1],
            [
                'platform_name'    => 'منصة الاقتصاد الأخضر',
                'support_email'    => 'support@greeneconomy.ye',
                'support_phone'    => '770000000',
                'maintenance_mode' => false,
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'تم جلب إعدادات المنصة بنجاح',
            'data'    => $settings,
        ], 200);
    }

    /**
     * تحديث إعدادات المنصة عبر FormRequest و Eloquent Model
     */
    public function update(UpdatePlatformSettingRequest $request)
    {
        $settings = PlatformSetting::updateOrCreate(
            ['id' => 1],
            $request->validated()
        );

        return response()->json([
            'status'  => true,
            'message' => 'تم حفظ إعدادات المنصة بنجاح',
            'data'    => $settings,
        ], 200);
    }
}
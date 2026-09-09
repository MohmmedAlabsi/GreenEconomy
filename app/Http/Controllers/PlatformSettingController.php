<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlatformSetting;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PlatformSettingController extends Controller
{
public function index()
    {
        // جلب أول سجل للإعدادات أو إنشاء سجل افتراضي في حال كان الجدول فارغاً
        $settings = DB::table('platform_settings')->first();

        if (!$settings) {
            $defaultData = [
                'platform_name'    => 'منصة الاقتصاد الأخضر',
                'support_email'    => 'support@greeneconomy.ye',
                'support_phone'    => '770000000',
                'maintenance_mode' => false,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            $id = DB::table('platform_settings')->insertGetId($defaultData);
            $settings = DB::table('platform_settings')->where('id', $id)->first();
        }

        // ضمان إرجاع قيمة maintenance_mode كقيمة منطقية (boolean)
        $settings->maintenance_mode = (bool) $settings->maintenance_mode;

        return response()->json([
            'status'  => true,
            'message' => 'تم جلب إعدادات المنصة بنجاح',
            'data'    => $settings,
        ], 200);
    }

    /**
     * تحديث إعدادات المنصة
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'platform_name'    => 'required|string|max:191',
            'support_email'    => 'nullable|email|max:191',
            'support_phone'    => 'nullable|string|max:50',
            'maintenance_mode' => 'required|boolean',
        ], [
            'platform_name.required'   => 'اسم المنصة مطلوب',
            'support_email.email'      => 'يرجى إدخال بريد إلكتروني صالح للدعم الفني',
            'maintenance_mode.boolean' => 'قيمة وضع الصيانة غير صالحة',
        ]);

        $settings = DB::table('platform_settings')->first();

        if ($settings) {
            DB::table('platform_settings')
                ->where('id', $settings->id)
                ->update([
                    'platform_name'    => $validated['platform_name'],
                    'support_email'    => $validated['support_email'],
                    'support_phone'    => $validated['support_phone'],
                    'maintenance_mode' => $validated['maintenance_mode'] ? 1 : 0,
                    'updated_at'       => now(),
                ]);
        } else {
            DB::table('platform_settings')->insert([
                'platform_name'    => $validated['platform_name'],
                'support_email'    => $validated['support_email'],
                'support_phone'    => $validated['support_phone'],
                'maintenance_mode' => $validated['maintenance_mode'] ? 1 : 0,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        $updatedSettings = DB::table('platform_settings')->first();
        $updatedSettings->maintenance_mode = (bool) $updatedSettings->maintenance_mode;

        return response()->json([
            'status'  => true,
            'message' => 'تم حفظ إعدادات المنصة بنجاح',
            'data'    => $updatedSettings,
        ], 200);
    }
}

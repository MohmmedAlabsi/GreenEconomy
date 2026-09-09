<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // 1. استثناء مسارات لوحة تحكم المسؤول حتى يتمكن من تسجيل الدخول وإيقاف الصيانة
        if ($request->is('api/admin/*') || $request->is('api/login')) {
            return $next($request);
        }

        // 2. فحص حالة الصيانة من جدول الإعدادات
        $settings = DB::table('platform_settings')->first();

        if ($settings && (bool) $settings->maintenance_mode) {
            // استثناء المستخدم إذا كان مسجلاً كمسؤول (Admin Role ID = 1)
            $user = $request->user();
            if ($user && ($user->role_id == 1 || $user->role === 'Admin')) {
                return $next($request);
            }

            return response()->json([
                'maintenance' => true,
                'message' => 'المنصة حالياً في وضع الصيانة والتطوير، يرجى المحاولة لاحقاً.',
                'support_email' => $settings->support_email,
                'support_phone' => $settings->support_phone,
            ], 503);
        }

        return $next($request);
    }
}
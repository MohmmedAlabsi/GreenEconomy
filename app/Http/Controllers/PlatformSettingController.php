<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlatformSetting;
use Illuminate\Routing\Controller;

class PlatformSettingController extends Controller
{
    public function index()
    {
        $settings = PlatformSetting::first();
        return response()->json($settings);
    }
}

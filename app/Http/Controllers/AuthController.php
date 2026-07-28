<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
   // عرض بيانات المستخدم الحالي مع أدواره وتخصصاته
    public function me(Request $request)
    {
        $user = $request->user()->load(['role', 'region', 'specialization', 'preferences']);
        return response()->json($user);
    }
}

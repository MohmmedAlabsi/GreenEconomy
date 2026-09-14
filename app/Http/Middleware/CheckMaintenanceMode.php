<?php

namespace App\Http\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
 
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode extends PreventRequestsDuringMaintenance
{
    protected $except = [
        '/',                   
        'login',               
        'api/login',           
        'api/platform_settings', 
    ];

    public function handle($request, Closure $next)
    {
        if ($this->app->isDownForMaintenance()) {
            if (Auth::check()) {
                $user = Auth::user();
                $isAdmin = ($user->role_id == 1) || (method_exists($user, 'hasRole') && $user->hasRole('admin'));

                if ($isAdmin) {
                    return $next($request);
                }
            }
        }

        return parent::handle($request, $next);
    }
}
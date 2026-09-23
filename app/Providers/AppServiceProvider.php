<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\FeasibilityStudy;
use App\Models\FieldVisit;
use App\Models\EngineerProfile;
use App\Models\PlatformSetting;
use App\Policies\FeasibilityStudyPolicy;
use App\Policies\FieldVisitPolicy;
use App\Policies\EngineerProfilePolicy;
use App\Policies\PlatformSettingPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(FeasibilityStudy::class, FeasibilityStudyPolicy::class);
        Gate::policy(FieldVisit::class, FieldVisitPolicy::class);
        Gate::policy(EngineerProfile::class, EngineerProfilePolicy::class);
        Gate::policy(PlatformSetting::class, PlatformSettingPolicy::class);

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}

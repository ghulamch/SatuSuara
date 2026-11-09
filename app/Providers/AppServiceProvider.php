<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        URL::forceScheme('http');

        // Superadmin full bypass for all RBAC permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        // Load election settings from storage/app/settings.json into config('election.*')
        $settingsPath = storage_path('app/settings.json');

        if (file_exists($settingsPath)) {
            $settings = json_decode(file_get_contents($settingsPath), true);

            if (is_array($settings)) {
                foreach ($settings as $key => $value) {
                    config(['election.' . $key => $value]);
                }

                // Apply dynamic Timezone
                $timezone = $settings['timezone'] ?? 'Asia/Jakarta';
                date_default_timezone_set($timezone);
                config(['app.timezone' => $timezone]);

                // Apply dynamic Language / Locale
                $locale = $settings['locale'] ?? 'id';
                App::setLocale($locale);
                Carbon::setLocale($locale);
                config(['app.locale' => $locale]);
            }
        } else {
            date_default_timezone_set('Asia/Jakarta');
            config(['app.timezone' => 'Asia/Jakarta']);
            App::setLocale('id');
            Carbon::setLocale('id');
            config(['app.locale' => 'id']);
        }
    }
}

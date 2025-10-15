<?php

namespace App\Providers;

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
        /*  // Forcer les URLs avec le sous-dossier
         if (request()->server('REQUEST_URI')) {
             $this->app['url']->forceRootUrl(config('app.url'));
         }

         // Pour les assets
         if (config('app.env') === 'production') {
             URL::forceScheme('http');
         }
             */
    }
}

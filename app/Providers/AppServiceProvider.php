<?php

namespace App\Providers;

use App\Auth\FirestoreUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\SEO\SeoManager::class, function ($app) {
            return new \App\Services\SEO\SeoManager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Auth::provider('firestore', function ($app, array $config) {
            return new FirestoreUserProvider($app->make(\App\Repositories\UserRepository::class));
        });
    }
}

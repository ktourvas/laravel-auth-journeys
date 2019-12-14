<?php

namespace laravel\auth\journeys;
use Illuminate\Support\ServiceProvider;

class LaravelAuthJourneysServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laj');

        if (! $this->app->routesAreCached()) {
            require __DIR__.'/routes/web.php';
        }

        $this->publishes([
            __DIR__.'/../config/auth-journeys.php' => config_path('auth-journeys.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app['router']->pushMiddlewareToGroup( 'web', \laravel\auth\journeys\Http\Middleware\InactiveLogout::class);

        $this->app['router']->pushMiddlewareToGroup( 'web', \laravel\auth\journeys\Http\Middleware\PasswordPolicy::class);

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(){}
}
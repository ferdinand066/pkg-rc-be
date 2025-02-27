<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/telescope';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });

        $this->routeManagement();
    }

    /**
     * List of all route that can be accessed on verified user.
     *
     * @return void
     */
    private function routeManagement()
    {
        Route::middleware(['api', 'auth:api', 'slow:1'])
            ->namespace($this->namespace)
            ->group(function () {
                Route::namespace($this->namespace)
                    ->prefix('admin')
                    ->as('admin.')
                    ->group(base_path('routes/roles/admin.php'));
                Route::namespace($this->namespace)
                    ->prefix('general')
                    ->as('general.')
                    ->group(base_path('routes/roles/general.php'));
            });

        Route::middleware(['api', 'reginacaeli.auth', 'slow:1'])
            ->namespace($this->namespace)
            ->group(function () {
                Route::namespace($this->namespace)
                    ->prefix('external')
                    ->as('external.')
                    ->group(base_path('routes/roles/external.php'));
            });
    }
}

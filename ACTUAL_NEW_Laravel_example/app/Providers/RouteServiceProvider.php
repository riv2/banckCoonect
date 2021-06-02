<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/report';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        Route::pattern('webhookmethod', '[a-z]+');

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace . '\Cloud')
            ->domain(config('app.domain'))
            ->group(base_path('routes/web_cloud.php'));


        if (config('app.gocpa_project') == 'cpadroid') {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web_cpadroid.php'));
        }

        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));

        Route::middleware([
                'web',
                'verified',
                'check_user_role:manager',
            ])
            ->namespace($this->namespace . '\Manager')
            ->as('manager.')
            ->prefix('/manager')
            ->group(base_path('routes/web_manager.php'));

        Route::middleware([
                'web',
                'check_is_pp',
                'verified',
                'check_user_role:advertiser',
            ])
            ->namespace($this->namespace . '\Advertiser')
            ->as('advertiser.')
            ->prefix('/advertiser')
            ->group(base_path('routes/web_advertiser.php'));

        Route::middleware([
                'web',
                'check_is_pp',
                'verified',
                'check_user_role:partner',
            ])
            ->namespace($this->namespace . '\Partner')
            ->as('partner.')
            ->prefix('/partner')
            ->group(base_path('routes/web_partner.php'));

        Route::middleware([
                'web',
                'check_is_pp',
                'verified',
                'check_user_role:analyst',
            ])
            ->namespace($this->namespace . '\Analyst')
            ->as('analyst.')
            ->prefix('/analyst')
            ->group(base_path('routes/web_analyst.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}

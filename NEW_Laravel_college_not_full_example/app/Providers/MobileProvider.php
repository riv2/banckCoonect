<?php
/**
 * User: Viktor Schepkin
 * Date: 2019-09-11
 * Time: 11:45
 */

namespace App\Providers;

use App\Factory\MobileFactory;
use Illuminate\View\ViewServiceProvider;

class MobileProvider extends ViewServiceProvider {

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }
    /**
     * Overwrite original so we can register MobileFactory
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->app->singleton('view', function($app)
        {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];

            $finder = $app['view.finder'];

            // IMPORTANT in next line you should use your ViewFactory
            $env = new MobileFactory($resolver, $finder, $app['events']);

            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $env->setContainer($app);

            $env->share('app', $app);

            return $env;
        });
    }
}
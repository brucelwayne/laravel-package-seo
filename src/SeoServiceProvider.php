<?php

namespace Brucelwayne\SEO;

use Illuminate\Config\Repository as Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SeoServiceProvider extends ServiceProvider
{
    protected $module_name = 'seo';

    public function register()
    {

    }


    function boot()
    {
        $this->bootConfigs();
        $this->bootMigrations();

        $this->app->extend('seotools.metatags', function ($command, $app) {
            return new SEOMeta(new Config($app['config']->get('seotools.meta', [])));
        });

        $this->bootRoutes();
    }

    protected function bootConfigs(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/seo.php', $this->module_name
        );
    }

    protected function bootMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    private function bootRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        Route::prefix('api')->middleware(['api'])->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }
}
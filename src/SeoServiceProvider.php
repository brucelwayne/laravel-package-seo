<?php

namespace Brucelwayne\SEO;

use Brucelwayne\SEO\Models\SeoPostModel;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\Eloquent\Relations\Relation;
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
        $this->bootRelationMaps();

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

    protected function bootRelationMaps()
    {
        Relation::enforceMorphMap([
            SeoPostModel::TABLE => SeoPostModel::class,
        ]);
    }

    private function bootRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        Route::prefix('api')->middleware(['api'])->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }
}
<?php

namespace Brucelwayne\SEO;

use Illuminate\Support\ServiceProvider;

class SeoServiceProvider extends ServiceProvider
{
    protected $module_name = 'seo';

    public function register()
    {
    }

    function boot(){
        $this->bootConfigs();
        $this->bootMigrations();
    }

    protected function bootConfigs(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/blog.php', $this->module_name
        );
    }

    protected function bootMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
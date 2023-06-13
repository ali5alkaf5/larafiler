<?php

namespace LaraFiler\Providers;

use Illuminate\Support\ServiceProvider;
use LaraFiler\Database\Factory;
use LaraFiler\LaraFiler;

class LaraFilerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('lara-filer', function ($app) {
            return new LaraFiler();
        });
        $this->mergeConfigFrom(__DIR__ . '/../../config/larafm.php', 'larafm');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->publishes([
            __DIR__ . '/../../config/larafm.php' => config_path('larafm.php'),
        ], 'config');
        $this->publishes([
            __DIR__ . '/../../database/migrations/CreateLarafmDocumentsTable.php' => database_path('migrations/2023_06_13_000000_create_larafm_documents_table.php'),
        ], 'migration');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }
}
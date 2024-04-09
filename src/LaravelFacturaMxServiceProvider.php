<?php

namespace JiagBrody\LaravelFacturaMx;

use JiagBrody\LaravelFacturaMx\Commands\LaravelFacturaMxCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelFacturaMxServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-factura-mx')
            ->hasConfigFile('factura-mx')
            ->hasViews()
            ->hasMigration('create_laravel-factura-mx_table')
            ->hasCommand(LaravelFacturaMxCommand::class);
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Migrations Provider need on boot() method
        |--------------------------------------------------------------------------
        */
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'factura-mx');
        $this->publishes([__DIR__ . '/../config/factura-mx.php' => config_path('factura-mx.php')], 'config');
    }
}

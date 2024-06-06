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
            ->hasConfigFile('laravel-factura-mx')
            ->hasViews()
            ->hasMigration('create_laravel-factura-mx_tables')
            ->hasCommand(LaravelFacturaMxCommand::class);
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Migrations Provider need on boot() method
        |--------------------------------------------------------------------------
        / List all: php artisan vendor:publish
        / Example: php artisan vendor:publish --provider="JiagBrody\LaravelFacturaMx\LaravelFacturaMxServiceProvider" --tag="laravel-factura-mx-views"
        /
        */
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-factura-mx');
        $this->loadViewsFrom(__DIR__.'/../resources/js/Pages', 'laravel-factura-mx');
        $this->publishes([__DIR__ . '/../config/laravel-factura-mx.php' => config_path('factura-mx.php')], 'laravel-factura-mx-config');
        $this->publishes([__DIR__.'/../resources/js/Pages/laravel-factura-mx' => resource_path('js/Pages/laravel-factura-mx')], 'laravel-factura-mx-views');
        $this->publishes([__DIR__.'/../resources/css/laravel-factura-mx' => resource_path('css/laravel-factura-mx')], 'laravel-factura-mx-styles');
    }
}

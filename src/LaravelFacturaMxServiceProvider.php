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
            ->name('jiagbrody-laravel-factura-mx')
            // ->hasConfigFile('jiagbrody-laravel-factura-mx')
            // ->hasViews()
            // ->hasMigration('create_laravel-factura-mx_table')
            // ->hasCommand(LaravelFacturaMxCommand::class)
        ;
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
        // $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-factura-mx', '');
        // $this->loadViewsFrom(__DIR__ . '/../resources/js/Pages', 'laravel-factura-mx');

        /**
         * View list all providers and tags: php artisan vendor:publish
         * Example of use for only 1 tag: php artisan vendor:publish --provider="JiagBrody\LaravelFacturaMx\LaravelFacturaMxServiceProvider" --tag="jiagbrody-laravel-factura-mx-inertia-views"
         * Example of use for copy all files: php artisan vendor:publish --provider="JiagBrody\LaravelFacturaMx\LaravelFacturaMxServiceProvider"
         *
         */
        $this->publishes([__DIR__ . '/../config/jiagbrody-laravel-factura-mx.php' => config_path('jiagbrody-laravel-factura-mx.php')], 'jiagbrody-laravel-factura-mx-config');
        $this->publishes([__DIR__ . '/../resources/js/Pages/jiagbrody-laravel-factura-mx' => resource_path('js/Pages/jiagbrody-laravel-factura-mx')], 'jiagbrody-laravel-factura-mx-inertia-views');
        $this->publishes([__DIR__ . '/../resources/css/jiagbrody-laravel-factura-mx' => resource_path('css/jiagbrody-laravel-factura-mx')], 'jiagbrody-laravel-factura-mx-tailwind-styles');
    }
}

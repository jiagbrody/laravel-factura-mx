<?php

namespace JiagBrody\LaravelFacturaMx;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use JiagBrody\LaravelFacturaMx\Commands\LaravelFacturaMxCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-factura-mx_table')
            ->hasCommand(LaravelFacturaMxCommand::class);
    }
}

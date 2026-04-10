<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx;

use Illuminate\Database\Eloquent\Relations\Relation;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelFacturaMxServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('jiagbrody-laravel-factura-mx')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoutes('web');
        // Nota: Si usas hasMigrations, Spatie las busca automáticamente
        // en database/migrations de tu paquete.
    }

    public function registeringPackage(): void
    {
        // Registramos el mapa en la fase de registro para que esté
        // disponible incluso antes del arranque (booting)
        Relation::enforceMorphMap([
            'invoice' => Invoice::class,
            'invoiceCfdi' => InvoiceCfdi::class,
        ]);
    }

    public function packageBooted(): void
    {
        // Aquí cargas lo que Spatie no maneja por convención
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publicaciones personalizadas
        $this->publishes([
            __DIR__.'/../resources/js/Pages/jiagbrody-laravel-factura-mx' => resource_path('js/Pages/jiagbrody-laravel-factura-mx')
        ], 'jiagbrody-laravel-factura-mx-inertia-views');
    }
}

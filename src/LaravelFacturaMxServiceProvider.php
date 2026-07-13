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
        // OJO: no usar hasConfigFile()/hasViews() sin argumento: shortName()
        // de laravel-package-tools recorta todo hasta "laravel-" y buscaría
        // "config/factura-mx.php", por lo que el config del paquete nunca se
        // fusionaría (config('jiagbrody-laravel-factura-mx') sería null para
        // cualquier app que no copie el archivo a su carpeta config/).
        $package
            ->name('jiagbrody-laravel-factura-mx')
            ->hasConfigFile('jiagbrody-laravel-factura-mx')
            ->hasViews('jiagbrody-laravel-factura-mx');
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
    }
}

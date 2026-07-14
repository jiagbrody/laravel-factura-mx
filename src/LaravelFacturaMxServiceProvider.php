<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx;

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

    // NOTA: el paquete NO registra un morph map. enforceMorphMap() es global:
    // obligaría a TODOS los modelos del app anfitrión a declarar alias, y las
    // instalaciones existentes ya persisten los morphs con el nombre completo
    // de la clase (comportamiento default de Laravel). Si el host quiere
    // alias, puede declararlos él mismo — con la migración de datos
    // correspondiente para los registros previos.

    public function packageBooted(): void
    {
        // Aquí cargas lo que Spatie no maneja por convención
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}

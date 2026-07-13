<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

use JiagBrody\LaravelFacturaMx\Exceptions\UnknownPacProviderException;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;

/**
 * Resuelve el proveedor PAC según el config "pac_chosen" y lo deja listo
 * con los datos de la empresa emisora de la factura.
 */
final class PacProviderFactory
{
    public static function make(Invoice $invoice): ProviderPacInterface
    {
        $chosen = (string) config('jiagbrody-laravel-factura-mx.pac_chosen');

        $provider = match ($chosen) {
            'finkok' => new FinkokPac($invoice),
            default => throw UnknownPacProviderException::for($chosen),
        };

        $provider->setInvoiceCompanyHelper($invoice->invoiceCompany);

        return $provider;
    }
}

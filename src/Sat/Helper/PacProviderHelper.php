<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;

class PacProviderHelper
{
    /*
     * SELECCIÓN DEL PAC A USAR ESTE PODRIA SER CAMBIADO DINAMICAMENTE
     */
    public function __invoke(Invoice $invoice): ?FinkokPac
    {
        if (config('factura-mx.pac_chosen') === 'finkok') {
            return new FinkokPac($invoice);
        }

        return null;
    }
}

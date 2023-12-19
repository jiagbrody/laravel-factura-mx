<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;

class PacProviderHelper
{
    protected FinkokPac $pacProvider;

    /*
     * SELECCIÓN DEL PAC A USAR ESTE PODRIA SER CAMBIADO DINAMICAMENTE
     */
    public function __construct(Invoice $invoice)
    {
        $this->pacProvider = new FinkokPac($invoice);
    }
}

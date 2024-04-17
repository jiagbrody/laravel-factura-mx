<?php

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;

final readonly class PacHelper
{
    public ?FinkokPac $pac;

    public function __invoke(): self
    {
        if (config('factura-mx.pac_chosen') === 'finkok') {
            $this->pac = new FinkokPac(new Invoice);
        }

        return $this;
    }
}

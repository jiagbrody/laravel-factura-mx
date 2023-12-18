<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteBuilder;

class LaravelFacturaMx
{
    public function create(): ComprobanteBuilder
    {
        return new ComprobanteBuilder();
    }

    public function stamp(Invoice $invoice)
    {
    }

    public function cancel()
    {
    }


    // public function recepcionDePagos(): PagoHandler
    // {
    //     return new PagoHandler;
    // }

    // public function traslado()
    // {
    //     // TODO: Implement traslado() method.
    // }

    // public function retencionesEInformacionDePagos()
    // {
    //     // TODO: Implement retencionesEInformacionDePagos() method.
    // }
}

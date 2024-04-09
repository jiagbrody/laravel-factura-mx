<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx;

use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteBuilder;

class LaravelFacturaMx
{
    public function create(): ComprobanteBuilder
    {
        date_default_timezone_set(config('factura-mx.default_timezone'));

        return new ComprobanteBuilder();
    }

    /*
     * Este se integró con el objeto de "create" del comprobante, como van de la mano, vi innecesario desacoplar.
     */
    //public function stamp(Invoice $invoice)
    //{
    //}
    //
    public function cancel()
    {
    }
}

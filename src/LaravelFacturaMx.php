<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx;

use JiagBrody\LaravelFacturaMx\Sat\Cancel\CancelInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Stamp\StampInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Status\StatusInvoiceBuilder;

class LaravelFacturaMx
{
    public function __construct()
    {
        date_default_timezone_set(config('jiagbrody-laravel-factura-mx.default_timezone'));
    }

    public function create(): ComprobanteBuilder
    {
        return new ComprobanteBuilder;
    }

    /*
     * Este se integró con el objeto de "create" del comprobante, como van de la mano, vi innecesario desacoplar.
     */
    //public function stamp(Invoice $invoice)
    //{
    //}
    //

    public function stamp(): StampInvoiceBuilder
    {
        return new StampInvoiceBuilder;
    }

    public function cancel(): CancelInvoiceBuilder
    {
        return new CancelInvoiceBuilder;
    }

    public function status(): StatusInvoiceBuilder
    {
        return new StatusInvoiceBuilder;
    }
}

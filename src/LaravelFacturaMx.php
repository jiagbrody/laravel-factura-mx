<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx;

use JiagBrody\LaravelFacturaMx\Helpers\BasicFunctionsHelper;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Cancel\CancelInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteTypes;
use JiagBrody\LaravelFacturaMx\Sat\Read\ReadInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Stamp\StampInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Status\StatusInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

class LaravelFacturaMx
{
    public function __construct()
    {
        date_default_timezone_set(config('jiagbrody-laravel-factura-mx.default_timezone'));
    }

    public function documentService(): DocumentService
    {
        return new DocumentService;
    }

    public function create(): ComprobanteTypes
    {
        return new ComprobanteTypes;
    }

    public function editDraft() {}

    public function invoiceStamper(Invoice $invoice): StampInvoiceBuilder
    {
        return new StampInvoiceBuilder(invoice: $invoice);
    }

    public function read(): ReadInvoiceBuilder
    {
        return new ReadInvoiceBuilder;
    }

    public function cancel(): CancelInvoiceBuilder
    {
        return new CancelInvoiceBuilder;
    }

    public function status(): StatusInvoiceBuilder
    {
        return new StatusInvoiceBuilder;
    }

    public function basicFunctionsHelper(): BasicFunctionsHelper
    {
        return new BasicFunctionsHelper;
    }
}

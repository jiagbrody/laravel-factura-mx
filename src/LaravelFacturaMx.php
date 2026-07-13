<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx;

use JiagBrody\LaravelFacturaMx\Helpers\BasicFunctionsHelper;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Cancel\CancelInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteTypes;
use JiagBrody\LaravelFacturaMx\Sat\Read\ReadInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Sat\RecoveryStampedXmlFile\RecoveryStampedXmlFileBuilder;
use JiagBrody\LaravelFacturaMx\Sat\SatCatalogsService;
use JiagBrody\LaravelFacturaMx\Sat\Stamp\StampInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Sat\Status\StatusInvoiceBuilder;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

class LaravelFacturaMx
{
    // OJO: este constructor ejecutaba date_default_timezone_set(), mutando la
    // zona horaria de TODO el proceso PHP del app anfitrión en cada
    // instanciación. Las fechas del paquete ahora usan la zona configurada
    // ("default_timezone") de forma explícita donde se generan.

    public function documentService(): DocumentService
    {
        return new DocumentService;
    }

    public function getCatalogService(): SatCatalogsService
    {
        return new SatCatalogsService;
    }

    public function create(): ComprobanteTypes
    {
        return new ComprobanteTypes;
    }

    // public function editDraft() {}

    public function invoiceStamper(Invoice $invoice): StampInvoiceBuilder
    {
        return new StampInvoiceBuilder(invoice: $invoice);
    }

    public function read(): ReadInvoiceBuilder
    {
        return new ReadInvoiceBuilder;
    }

    public function invoiceCanceller(Invoice $invoice): CancelInvoiceBuilder
    {
        return new CancelInvoiceBuilder(invoice: $invoice);
    }

    public function status(): StatusInvoiceBuilder
    {
        return new StatusInvoiceBuilder;
    }

    public function RecoveryCfdiXmlFile(): RecoveryStampedXmlFileBuilder
    {
        return new RecoveryStampedXmlFileBuilder;
    }

    public function basicFunctionsHelper(): BasicFunctionsHelper
    {
        return new BasicFunctionsHelper;
    }
}

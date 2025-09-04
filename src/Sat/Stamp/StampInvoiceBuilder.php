<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Stamp;

use JiagBrody\LaravelFacturaMx\Actions\UpdateRecordsWhenStampingRevenueInvoiceAction;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;

class StampInvoiceBuilder
{
    protected readonly FinkokPac $pacProvider;

    protected PacStampResponse $stampResponse;

    public function __construct(public readonly Invoice $invoice)
    {
        // VALIDAR SI SE QUITA EL INVOICE, YA LO TENGO DECLARADO COMO "$this-invoice"
        $this->pacProvider = new FinkokPac($invoice);
        $this->pacProvider->setInvoiceCompanyHelper($invoice->invoiceCompany);
    }

    public function build(): PacStampResponse
    {
        return $this->pacProvider->stampInvoice();
    }

    // public function getStampResponse(): PacStampResponse
    // {
    //     return $this->stampResponse;
    // }
}

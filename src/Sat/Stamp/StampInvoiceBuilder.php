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

    public function __construct(readonly Invoice $invoice)
    {
        $this->pacProvider = new FinkokPac($invoice);
        $this->pacProvider->setInvoiceCompanyHelper($invoice->invoiceCompany);
    }

    public function build(): PacStampResponse
    {
        $this->stampResponse = $this->pacProvider->stampInvoice();

        if ($this->stampResponse->getCheckProcess()) {
            (new UpdateRecordsWhenStampingRevenueInvoiceAction)(
                invoice: $this->invoice,
                uuid: $this->stampResponse->getUuid(),
                xml: $this->stampResponse->getXml(),
                fileName: null
            );
        }

        return $this->stampResponse;
    }
}

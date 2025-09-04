<?php

namespace JiagBrody\LaravelFacturaMx\Sat\RecoveryStampedXmlFile;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacRecoveryCfdiXmlResponse;

class RecoveryStampedXmlFileBuilder
{
    protected FinkokPac $pacProvider;

    protected Invoice $invoice;

    public function setInvoice(Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function setPacProvider(): self
    {
        $this->pacProvider = new FinkokPac($this->invoice);
        $this->pacProvider->setInvoiceCompanyHelper($this->invoice->invoiceCompany);

        return $this;
    }

    public function build(): PacRecoveryCfdiXmlResponse
    {
        return $this->pacProvider->getXmlStamped();
    }
}

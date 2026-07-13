<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Status;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStatusResponse;

final class StatusInvoiceBuilder
{
    protected Invoice $invoice;

    protected string $receptorRfc;

    protected string $total;

    protected FinkokPac $pacProvider;

    protected PacStatusResponse $statusResponse;

    public function setInvoice(Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function setPacProvider(): self
    {
        $this->pacProvider = new FinkokPac($this->invoice);
        $this->pacProvider->setInvoiceCompanyHelper($this->invoice->invoiceCompany);
        $this->pacProvider->setReceptorRfc($this->receptorRfc);
        $this->pacProvider->setTotal($this->total);

        return $this;
    }

    public function setReceptorRfc(string $receptorRfc): void
    {
        $this->receptorRfc = $receptorRfc;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function build(): PacStatusResponse
    {
        $this->statusResponse = $this->pacProvider->statusInvoice();

        return $this->statusResponse;
    }
}

<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Status;

use JiagBrody\LaravelFacturaMx\Actions\UpdateRecordsAfterCheckingInvoiceStatusAction;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStatusResponse;

final readonly class StatusInvoiceBuilder
{
    protected Invoice $invoice;

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
        $this->pacProvider->setTotal($this->total);

        return $this;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function build(): PacStatusResponse
    {
        $this->statusResponse = $this->pacProvider->statusInvoice();

        if (($this->statusResponse->getCheckProcess() && $this->statusResponse->getInvoiceStatusEnum() === InvoiceStatusEnum::CANCELED) && $this->invoice->invoice_status_id !== InvoiceStatusEnum::CANCELED->value) {
            (new UpdateRecordsAfterCheckingInvoiceStatusAction)($this->invoice);
        }

        return $this->statusResponse;
    }
}

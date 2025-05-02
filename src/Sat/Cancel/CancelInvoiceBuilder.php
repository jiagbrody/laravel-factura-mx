<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Cancel;

use JiagBrody\LaravelFacturaMx\Actions\UpdateRecordsIfTheInvoiceHasBeenSentByThePacToCancelAction;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;

final class CancelInvoiceBuilder
{
    protected readonly FinkokPac $pacProvider;

    protected readonly InvoiceCfdiCancelTypeEnum $cancelTypeEnum;

    protected ?InvoiceCfdi $replacementInvoiceCfdi = null;

    protected ?string $replacementUUID = null;

    protected readonly PacCancelResponse $cancelResponse;

    protected PacStampResponse $stampResponse;

    public function __construct(readonly Invoice $invoice)
    {
        // VALIDAR SI SE QUITA EL INVOICE, YA LO TENGO DECLARADO COMO "$this-invoice"
        $this->pacProvider = new FinkokPac($invoice);
        $this->pacProvider->setInvoiceCompanyHelper($invoice->invoiceCompany);
    }

    // public function setInvoice(Invoice $invoice): self
    // {
    //     $this->invoice = $invoice;
    //
    //     return $this;
    // }

    // public function setPacProvider(): self
    // {
    //     $this->pacProvider = new FinkokPac($this->invoice);
    //     $this->pacProvider->setInvoiceCompanyHelper($this->invoice->invoiceCompany);
    //
    //     return $this;
    // }

    public function setCancelTypeEnum(InvoiceCfdiCancelTypeEnum $cancelTypeEnum): self
    {
        $this->cancelTypeEnum = $cancelTypeEnum;

        return $this;
    }

    public function getCancelTypeEnum(): InvoiceCfdiCancelTypeEnum
    {
        return $this->cancelTypeEnum;
    }

    public function setReplacementInvoiceCfdi(InvoiceCfdi $invoiceCfdi): self
    {
        $this->replacementInvoiceCfdi = $invoiceCfdi;
        $this->replacementUUID = $invoiceCfdi->uuid;

        return $this;
    }

    public function build(): PacCancelResponse
    {
        $this->cancelResponse = $this->pacProvider->cancelInvoice(cfdiCancelTypeEnum: $this->cancelTypeEnum, replacementUUID: $this->replacementUUID);

        if ($this->cancelResponse->checkProcess) {
            (new UpdateRecordsIfTheInvoiceHasBeenSentByThePacToCancelAction)->make(
                invoiceCfdi: $this->invoice->invoiceCfdi,
                cancelTypeEnum: $this->cancelTypeEnum,
                replacementInvoiceCfdi: $this->replacementInvoiceCfdi,
                xmlFile: $this->cancelResponse->acuse,
                fileName: 'acuse-cancelacion'.'_'.$this->invoice->invoiceCfdi->uuid.'_'.date('Y-m-d-H_i_s')
            );
        }

        return $this->cancelResponse;
    }
}

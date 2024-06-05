<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Cancel;

use JiagBrody\LaravelFacturaMx\Actions\UpdateRecordsIfTheInvoiceHasBeenSentByThePacToCancelAction;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacCancelResponse;

final class CancelInvoiceBuilder
{
    protected readonly Invoice $invoice;

    protected readonly FinkokPac $pacProvider;

    protected readonly InvoiceCfdiCancelTypeEnum $cancelTypeEnum;

    protected ?string $replacementUUID;

    protected readonly PacCancelResponse $cancelResponse;

    public function __construct()
    {
        $this->replacementUUID = null;
    }

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

    public function setCancelTypeEnum(InvoiceCfdiCancelTypeEnum $cancelTypeEnum): self
    {
        $this->cancelTypeEnum = $cancelTypeEnum;

        return $this;
    }

    public function setReplacementUUID(string $replacementUUID): void
    {
        $this->replacementUUID = $replacementUUID;
    }

    public function build(): PacCancelResponse
    {
        $this->cancelResponse = $this->pacProvider->cancelInvoice(cfdiCancelTypeEnum: $this->cancelTypeEnum, replacementUUID: $this->replacementUUID);

        if ($this->cancelResponse->checkProcess) {
            (new UpdateRecordsIfTheInvoiceHasBeenSentByThePacToCancelAction())(
                invoiceCfdi: $this->invoice->invoiceCfdi,
                cancelTypeEnum: $this->cancelTypeEnum,
                xmlFile: $this->cancelResponse->acuse,
                fileName: 'invoice-cfdi' . '_' . $this->invoice->invoiceCfdi->id . '_' . date('Y-m-d\tHis')
            );
        }

        return $this->cancelResponse;
    }
}

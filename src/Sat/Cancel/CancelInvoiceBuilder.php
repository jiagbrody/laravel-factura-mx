<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Cancel;

use JiagBrody\LaravelFacturaMx\Actions\UpdateRecordsIfTheInvoiceHasBeenSentByThePacToCancelAction;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacProviderFactory;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\ProviderPacInterface;

final class CancelInvoiceBuilder
{
    protected readonly ProviderPacInterface $pacProvider;

    protected readonly InvoiceCfdiCancelTypeEnum $cancelTypeEnum;

    protected ?InvoiceCfdi $replacementInvoiceCfdi = null;

    protected ?string $replacementUUID = null;

    protected readonly PacCancelResponse $cancelResponse;

    public function __construct(public readonly Invoice $invoice)
    {
        $this->pacProvider = PacProviderFactory::make($invoice);
    }

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

        // El caso "UUID previamente cancelado" (202) es exitoso pero sin acuse:
        // el recibo original se conserva y no hay nada nuevo que persistir.
        if ($this->cancelResponse->checkProcess && $this->cancelResponse->hasAcuse()) {
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

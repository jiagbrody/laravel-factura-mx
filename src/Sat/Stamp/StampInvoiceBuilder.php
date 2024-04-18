<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Stamp;

use JiagBrody\LaravelFacturaMx\Actions\UpdateRecordsWhenStampingRevenueInvoiceAction;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;

class StampInvoiceBuilder
{
    protected readonly Invoice $invoice;

    protected readonly FinkokPac $pacProvider;

    protected PacStampResponse $stampResponse;

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

    public function build(): PacStampResponse
    {
        $this->stampResponse = $this->pacProvider->stampInvoice();

        if ($this->stampResponse->getCheckProcess()) {
            (new UpdateRecordsWhenStampingRevenueInvoiceAction())(
                invoice: $this->invoice,
                uuid: $this->stampResponse->getUuid(),
                xml: $this->stampResponse->getXml(),
                fileName: null
            );
        }

        return $this->stampResponse;
    }
    // protected PacHelper $pacHelper;

    // protected readonly PacStampResponse $response;

    // protected readonly CreateStampConcrete $concrete;

    // public function __construct(protected Invoice $invoice)
    // {
    //     $this->pacHelper = (new PacHelper)($invoice);
    // }

    // public function sendRequestToStamp(): void
    // {
    //     // TODO: CAMBIAR ESTE PROCESO POR "$this->pacHelper->pac->stampInvoice()". Y NO USAR DATOS DE PRUEBA.
    //     // $this->response = $this->pacHelper->pac->getStampTestData()->success()->getResponse();
    //     $this->response = $this->pacHelper->pac->stampInvoice();
    //     if ($this->response->getCheckProcess()) {
    //
    //         $this->concrete = new CreateStampConcrete(
    //             invoice: $this->invoice,
    //             uuid: $this->response->getUuid(),
    //             xml: $this->response->getXml()
    //         );
    //
    //     }
    // }

    // public function updateInvoiceByStamp(): void
    // {
    //     $this->concrete->updateInvoice();
    // }

    // public function saveCfdi(): void
    // {
    //     $this->concrete->createCfdi();
    // }

    // public function saveDocumentByStamp(?string $fileName = null): void
    // {
    //     (new DocumentHandler())->update(
    //         relationshipModel: $this->invoice->getMorphClass(),
    //         relationshipId: $this->invoice->id,
    //         fileName: ($fileName === null) ? '' : $fileName,
    //         fileContent: $this->response->getXml()
    //     );
    // }
}

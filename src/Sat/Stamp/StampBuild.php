<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Stamp;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Document\DocumentHandler;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;

class StampBuild implements StampBuildInterface
{
    protected FinkokPac $pac;

    protected readonly PacStampResponse $response;

    protected readonly CreateStampConcrete $concrete;

    public function __construct(protected Invoice $invoice)
    {
        if (config('factura-mx.pac_chosen') === 'finkok') {
            $this->pac = new FinkokPac($invoice);
        }
    }

    public function sendRequestToStamp(): void
    {
        // TODO: CAMBIAR ESTE PROCESO POR "$this->pac->stampInvoice()". Y NO USAR DATOS DE PRUEBA.
        // $this->response = $this->pac->getStampTestData()->success()->getResponse();
        $this->response = $this->pac->stampInvoice();
        if ($this->response->getCheckProcess()) {

            $this->concrete = new CreateStampConcrete(
                invoice: $this->invoice,
                uuid: $this->response->getUuid(),
                xml: $this->response->getXml()
            );

        }
    }

    public function updateInvoiceByStamp(): void
    {
        $this->concrete->updateInvoice();
    }

    public function saveCfdi(): void
    {
        $this->concrete->createCfdi();
    }

    public function saveDocumentByStamp(?string $fileName = null): void
    {
        (new DocumentHandler())->update(
            relationshipModel: $this->invoice->getMorphClass(),
            relationshipId: $this->invoice->id,
            fileName: ($fileName === null) ? "" : $fileName,
            fileContent: $this->response->getXml()
        );
    }
}

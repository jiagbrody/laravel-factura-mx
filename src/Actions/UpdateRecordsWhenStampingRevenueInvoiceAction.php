<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Actions;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Sat\Document\DocumentHandler;

class UpdateRecordsWhenStampingRevenueInvoiceAction
{
    public function __invoke(
        Invoice $invoice,
        string $uuid,
        string $xml,
        ?string $fileName
    ) {
        DB::transaction(function () use ($invoice, $uuid, $xml, $fileName) {
            $this->updateInvoice(invoice: $invoice);
            $this->createCfdi(invoice: $invoice, uuid: $uuid);
            $this->generateDocuments(invoice: $invoice, xmlContent: $xml, fileName: $fileName);
        });
    }

    public function updateInvoice(Invoice $invoice): void
    {
        $invoice->invoice_status_id = InvoiceStatusEnum::VIGENT->value;
        $invoice->save();
        $invoice->load('invoiceDetail');
    }

    public function createCfdi($invoice, $uuid): void
    {
        $invoiceCfdi = new InvoiceCfdi;
        $invoiceCfdi->user_id = auth()->id();
        $invoiceCfdi->invoice_id = $invoice->id;
        $invoiceCfdi->uuid = $uuid;
        $invoiceCfdi->save();

        $invoice->refresh();
    }

    public function generateDocuments($invoice, $xmlContent, $fileName): void
    {
        (new DocumentHandler)->create(
            relationshipModel: $invoice->invoiceCfdi->getMorphClass(),
            relationshipId: $invoice->invoiceCfdi->id,
            documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
            fileName: ($fileName === null) ? 'invoice-'.$invoice->id.'-cfdi-'.$invoice->invoiceCfdi->id.'-'.$invoice->invoiceCfdi->uuid : $fileName,
            filePath: 'files/cfdis',
            mimeType: InvoiceDocumentTypeEnum::XML_FILE->getMimeType(),
            extension: InvoiceDocumentTypeEnum::XML_FILE->getExtension(),
            storage: 'public',
            fileContent: $xmlContent,
        );

        // $xml = (new XmlFileSatHelperBuilder($invoice))
        //     ->updateModel($invoice->cfdi)
        //     ->updatePath()
        //     ->updateFileName('invoice-' . $invoice->id . '-' . $invoice->cfdi->uuid)
        //     ->generate($xmlContent);

        // (new PdfFileSatHelperBuilder())
        //     ->setInvoiceCfdiType($invoice->invoice_cfdi_type_id)
        //     ->setXmlContent($xmlContent)
        //     ->setXmlDocument($xml)
        //     ->build();

        // BORRO LOS DOCUMENTOS DE BORRADOR.
        // if ($invoice->documents()->exists()) {
        //     $invoice->documents()->each(function ($document) {
        //         (new DocumentDestroyService($document))->make();
        //     });
        // }
    }
}

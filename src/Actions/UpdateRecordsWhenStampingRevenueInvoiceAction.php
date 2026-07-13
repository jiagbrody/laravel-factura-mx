<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Actions;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;

class UpdateRecordsWhenStampingRevenueInvoiceAction
{
    public function __invoke(
        Invoice $invoice,
        string $uuid,
        string $xml,
        ?string $fileName
    ): void {
        DB::transaction(function () use ($invoice, $uuid, $xml, $fileName) {
            $this->updateInvoice(invoice: $invoice);
            $this->createCfdi(invoice: $invoice, uuid: $uuid);
            $this->generateDocuments(invoice: $invoice, xmlContent: $xml, fileName: $fileName);
            $this->deleteDraftDocuments(invoice: $invoice);
        });
    }

    public function updateInvoice(Invoice $invoice): void
    {
        $invoice->invoice_status_id = InvoiceStatusEnum::VIGENT->value;
        $invoice->save();
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

    public function generateDocuments(Invoice $invoice, $xmlContent, $fileName): void
    {
        (new DocumentRepository)->create(
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
    }

    private function deleteDraftDocuments(Invoice $invoice): void
    {
        $invoice->invoiceDocuments->each(function (InvoiceDocument $document) {
            (new DocumentRepository)->delete(invoiceDocument: $document);
        });
    }
}

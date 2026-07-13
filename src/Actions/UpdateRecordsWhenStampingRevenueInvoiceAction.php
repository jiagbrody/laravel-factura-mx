<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Actions;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceTypeEnum;
use JiagBrody\LaravelFacturaMx\Exceptions\FacturaMxException;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;
use JiagBrody\LaravelFacturaMx\Sat\Helper\ConvertXmlContentToObjectHelper;
use JiagBrody\LaravelFacturaMx\Sat\Helper\GeneratePdfDocumentFromXmlObjectForIngresoHelper;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

/**
 * Única implementación del post-timbrado: marca la factura como vigente,
 * registra el CFDI (UUID), guarda el XML timbrado, genera el PDF legible y
 * elimina los documentos de borrador. La invoca StampInvoiceBuilder cuando
 * el PAC confirma el timbrado; el app anfitrión NO debe llamarla por
 * separado.
 */
class UpdateRecordsWhenStampingRevenueInvoiceAction
{
    public function __invoke(
        Invoice $invoice,
        string $uuid,
        string $xml,
        ?string $fileName = null
    ): void {
        if (trim($xml) === '') {
            throw new FacturaMxException('El PAC confirmó el timbrado (UUID: '.$uuid.') pero no devolvió el XML y tampoco fue posible recuperarlo con get_xml; no se persistió nada localmente. Recupera el XML con LaravelFacturaMx::RecoveryCfdiXmlFile() o desde el portal del PAC.');
        }

        DB::transaction(function () use ($invoice, $uuid, $xml, $fileName) {
            $this->updateInvoice(invoice: $invoice);
            $this->createCfdi(invoice: $invoice, uuid: $uuid);
            $xmlDocument = $this->createXmlDocument(invoice: $invoice, xmlContent: $xml, fileName: $fileName);
            $this->createPdfDocument(invoice: $invoice, xmlDocument: $xmlDocument, xmlContent: $xml);
            $this->deleteDraftDocuments(invoice: $invoice);
        });
    }

    private function updateInvoice(Invoice $invoice): void
    {
        $invoice->invoice_status_id = InvoiceStatusEnum::VIGENT->value;
        $invoice->save();
    }

    private function createCfdi(Invoice $invoice, string $uuid): void
    {
        $invoiceCfdi = new InvoiceCfdi;
        $invoiceCfdi->user_id = auth()->id();
        $invoiceCfdi->invoice_id = $invoice->id;
        $invoiceCfdi->uuid = $uuid;
        $invoiceCfdi->save();

        $invoice->refresh();
    }

    private function createXmlDocument(Invoice $invoice, string $xmlContent, ?string $fileName): InvoiceDocument
    {
        return (new DocumentRepository)->create(
            relationshipModel: $invoice->invoiceCfdi->getMorphClass(),
            relationshipId: $invoice->invoiceCfdi->id,
            documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
            fileName: $fileName ?? 'invoice-'.$invoice->id.'-cfdi-'.$invoice->invoiceCfdi->id.'-'.$invoice->invoiceCfdi->uuid,
            filePath: (string) config('jiagbrody-laravel-factura-mx.invoices_files_path', 'sat-documents/invoices'),
            mimeType: InvoiceDocumentTypeEnum::XML_FILE->getMimeType(),
            extension: InvoiceDocumentTypeEnum::XML_FILE->getExtension(),
            storage: (string) config('jiagbrody-laravel-factura-mx.filesystem_disk', 'local'),
            fileContent: $xmlContent,
        );
    }

    private function createPdfDocument(Invoice $invoice, InvoiceDocument $xmlDocument, string $xmlContent): void
    {
        // El host puede ser dueño de su propio PDF (vista y datos propios);
        // en ese caso el paquete no genera ninguno.
        if (! config('jiagbrody-laravel-factura-mx.generate_pdf_on_stamp', true)) {
            return;
        }

        // La vista actual del PDF está diseñada para ingreso/egreso; para
        // otros tipos de comprobante no se genera PDF (por ahora).
        if (! in_array((int) $invoice->invoice_type_id, [InvoiceTypeEnum::INGRESO->value, InvoiceTypeEnum::EGRESO->value], true)) {
            return;
        }

        $comprobante = ConvertXmlContentToObjectHelper::make($xmlContent, true);
        $pdfContent = (new GeneratePdfDocumentFromXmlObjectForIngresoHelper)($comprobante);

        (new DocumentService)->createPdfDocumentFromXmlFile($xmlDocument, $pdfContent);
    }

    private function deleteDraftDocuments(Invoice $invoice): void
    {
        $invoice->invoiceDocuments->each(function (InvoiceDocument $document) {
            (new DocumentRepository)->delete(invoiceDocument: $document);
        });
    }
}

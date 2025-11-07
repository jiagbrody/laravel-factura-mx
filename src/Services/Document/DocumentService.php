<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services\Document;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdiCancelReceipt;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\CreateDocument;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;

class DocumentService
{
    protected readonly Invoice $invoice;

    public readonly bool $hasCfdi;

    public readonly bool $hasCfdiCanceled;

    protected Collection $documents;

    // protected Collection $cancellationDocuments;

    protected Collection $cancelReceiptDocuments;

    protected InvoiceDocument $xmlFile;

    protected InvoiceDocument $pdfFile;

    public readonly Helpers $helpers;

    public function __construct()
    {
        $this->helpers = new Helpers;
        $this->documents = new Collection;
        $this->xmlFile = new InvoiceDocument;
        $this->pdfFile = new InvoiceDocument;
    }

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->hasCfdi = (bool)$invoice->invoiceCfdi;
        $this->hasCfdiCanceled = (bool)$invoice->invoiceCfdi?->invoiceCfdiCancel;

        if ($this->hasCfdi) {
            // OBTIENE LOS ARCHIVOS DEL CFDI
            $this->documents = $invoice->invoiceCfdi->invoiceDocuments ?? new Collection;
            $this->xmlFile = $invoice->invoiceCfdi->xmlInvoiceDocument ?? new InvoiceDocument;
            $this->pdfFile = $invoice->invoiceCfdi->pdfInvoiceDocument ?? new InvoiceDocument;
            $this->cancelReceiptDocuments = InvoiceCfdiCancelReceipt::with([
                'invoiceDocuments',
                'invoiceCfdiCancelType',
                'replacementInvoiceCfdi',
            ])->where('invoice_cfdi_id', $invoice->invoiceCfdi->id)->get();
        } else {
            // OBTIENE BORRADOR
            $this->documents = $invoice->invoiceDocuments ?? new Collection;
            $this->xmlFile = $invoice->xmlInvoiceDocument ?? new InvoiceDocument;
            $this->pdfFile = $invoice->pdfInvoiceDocument ?? new InvoiceDocument;
            $this->cancelReceiptDocuments = new Collection;
        }
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function getDocuments(): \Illuminate\Support\Collection
    {
        return $this->documents;
    }

    public function getCancelReceiptDocuments(): Collection
    {
        return $this->cancelReceiptDocuments;
    }

    public function getXmlFile(): InvoiceDocument
    {
        return $this->xmlFile;
    }

    public function getXmlArray()
    {
        return $this->helpers::obtainXmlDocumentObject(document: $this->xmlFile, associative: true);
    }

    public function getXmlObject(): bool|\stdClass
    {
        return $this->helpers::obtainXmlDocumentObject(document: $this->xmlFile);
    }

    public function getXmlUrl()
    {
        return ($this->xmlFile->exists) ? $this->xmlFile->public_url : '';
    }

    public function getPdfFile(): InvoiceDocument
    {
        return $this->pdfFile;
    }

    public function getPdfUrl()
    {
        return ($this->pdfFile->exists) ? $this->pdfFile->public_url : '';
    }

    /**
     * @throws Exception
     */
    public function createXmlDocument(Invoice|InvoiceCfdi|InvoiceCfdiCancelReceipt $modelToSave, string $filePath, string $fileName, string $storage, string $xmlContent): InvoiceDocument
    {
        return (new CreateDocument(
            relationshipModel: $modelToSave->getMorphClass(),
            relationshipId: $modelToSave->id,
            documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
            fileName: $fileName,
            filePath: $filePath,
            mimeType: 'xml',
            extension: 'xml',
            storage: $storage,
            fileContent: $xmlContent
        ))();
    }

    /**
     * @throws Exception
     */
    public function createPdfDocumentFromXmlFile(InvoiceDocument $xmlFile, string $pdfContent): InvoiceDocument
    {
        return (new CreateDocument(
            relationshipModel: $xmlFile->documentable_type,
            relationshipId: $xmlFile->documentable_id,
            documentTypeId: InvoiceDocumentTypeEnum::PDF_FILE->value,
            fileName: $xmlFile->file_name,
            filePath: $xmlFile->file_path,
            mimeType: 'pdf',
            extension: 'pdf',
            storage: $xmlFile->storage,
            fileContent: $pdfContent
        ))();
    }

    /**
     * @throws Exception
     */
    public function regenerateAndSaveInvoicePdf(InvoiceDocument $invoiceDocument, string $documentPdf): void
    {
        $documentRepository = new DocumentRepository;
        $documentRepository->update(
            invoiceDocument: $invoiceDocument,
            relationshipModel: $invoiceDocument->documentable_type,
            relationshipId: $invoiceDocument->documentable_id,
            documentTypeId: InvoiceDocumentTypeEnum::PDF_FILE->value,
            fileName: $invoiceDocument->file_name,
            filePath: $invoiceDocument->file_path,
            mimeType: $invoiceDocument->mime_type,
            extension: $invoiceDocument->extension,
            storage: $invoiceDocument->storage,
            fileContent: $documentPdf,
            overwriteFileOnDisk: true
        );
    }
}

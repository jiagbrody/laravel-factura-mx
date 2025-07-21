<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services\Document;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdiCancelReceipt;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;
use JiagBrody\LaravelFacturaMx\Sat\Helper\GeneratePdfDocumentFromXmlObjectForIngresoHelper;

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
        $this->hasCfdi = (bool) $invoice->invoiceCfdi;
        $this->hasCfdiCanceled = (bool) $invoice->invoiceCfdi?->invoiceCfdiCancel;

        if ($this->hasCfdi) {
            // OBTIENE LOS ARCHIVOS DEL CFDI
            $this->documents = $invoice->invoiceCfdi->invoiceDocuments ?? new Collection;
            $xmlFile = $this->documents->where('invoice_document_type_id', InvoiceDocumentTypeEnum::XML_FILE->value)->first();
            $pdfFile = $this->documents->where('invoice_document_type_id', InvoiceDocumentTypeEnum::PDF_FILE->value)->first();
            $this->xmlFile = (! is_null($xmlFile) ? $xmlFile : new InvoiceDocument);
            $this->pdfFile = (! is_null($pdfFile)) ? $pdfFile : new InvoiceDocument;
            // $this->xmlFile = $invoice->invoiceCfdi->xmlInvoiceDocument ?? new InvoiceDocument;
            // $this->pdfFile = $invoice->invoiceCfdi->pdfInvoiceDocument ?? new InvoiceDocument;
            // $this->cancellationDocuments = $invoice->invoiceCfdi->invoiceCfdiCancel->invoiceDocuments ?? new Collection;
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
            // $this->cancellationDocuments = new Collection;
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

    // public function getCancellationDocuments(): Collection
    // {
    //     return $this->cancellationDocuments;
    // }

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

    public function regenerateAndSaveInvoicePdf(string $filenamePdf = '', $documentPdf = null): void
    {
        $model = ($this->invoice->invoiceCfdi) ? $this->invoice->invoiceCfdi->getMorphClass() : $this->invoice->getMorphClass();
        $id = ($this->invoice->invoiceCfdi) ? $this->invoice->invoiceCfdi->id : $this->invoice->id;

        if ($documentPdf === null) {
            $documentPdf = (new GeneratePdfDocumentFromXmlObjectForIngresoHelper)(comprobante: (array) $this->getXmlArray());
        }

        $documentRepository = new DocumentRepository;
        $documentRepository->create(
            relationshipModel: $model,
            relationshipId: $id,
            documentTypeId: InvoiceDocumentTypeEnum::PDF_FILE->value,
            fileName: $this->getFileNameToSave((($filenamePdf !== '')) ? $filenamePdf : $this->xmlFile->file_name),
            filePath: config('jiagbrody-laravel-factura-mx.invoices_files_path'),
            mimeType: 'pdf',
            extension: 'pdf',
            storage: config('jiagbrody-laravel-factura-mx.filesystem_disk'),
            fileContent: $documentPdf
        );
    }

    /*
     * Formato de nombre para guardar
     */
    public function getFileNameToSave(string $customFileName = ''): string
    {
        if ($customFileName === '') {
            $customFileName = 'invoice-'.$this->invoice->id;
        }

        return Str::slug($customFileName);
    }
}

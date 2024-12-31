<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services\Document;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;
use JiagBrody\LaravelFacturaMx\Sat\Helper\GeneratePdfDocumentFromXmlObjectForIngresoHelper;

class DocumentService
{
    protected readonly Invoice $invoice;

    public readonly bool $hasCfdi;

    protected Collection $documents;

    protected InvoiceDocument $xmlFile;

    protected InvoiceDocument $pdfFile;

    public readonly Helpers $helpers;

    public function __construct()
    {
        $this->helpers = new Helpers();
        $this->documents = new Collection;
        $this->xmlFile = new InvoiceDocument;
        $this->pdfFile = new InvoiceDocument;
    }

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->hasCfdi = (bool)$invoice->invoiceCfdi;

        if ($this->hasCfdi) {
            //OBTIENE LOS ARCHIVOS DEL CFDI
            $this->documents = $invoice->invoiceCfdi->invoiceDocuments ?? new Collection;
            $this->xmlFile = $invoice->invoiceCfdi->xmlInvoiceDocument ?? new InvoiceDocument;
            $this->pdfFile = $invoice->invoiceCfdi->pdfInvoiceDocument ?? new InvoiceDocument;
        } else {
            //OBTIENE BORRADOR
            $this->documents = $invoice->invoiceDocuments ?? new Collection;
            $this->xmlFile = $invoice->xmlInvoiceDocument ?? new InvoiceDocument;
            $this->pdfFile = $invoice->pdfInvoiceDocument ?? new InvoiceDocument;
        }
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function getDocuments(): Collection
    {
        return $this->documents;
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

    public function regenerateAndSaveInvoicePdf(string $filenamePdf = ''): void
    {
        $model = ($this->invoice->invoiceCfdi) ? $this->invoice->invoiceCfdi->getMorphClass() : $this->invoice->getMorphClass();
        $id = ($this->invoice->invoiceCfdi) ? $this->invoice->invoiceCfdi->id : $this->invoice->id;

        $documentPdf = (new GeneratePdfDocumentFromXmlObjectForIngresoHelper)(comprobante: (array)$this->getXmlArray());
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
    public function getFileNameToSave(?string $customFileName = null): string
    {
        if ($customFileName === null) {
            $customFileName = 'invoice-' . $this->invoice->id;
        }

        return Str::slug($customFileName);
    }
}

<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Services\Document;

use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;

class DocumentService extends InvoiceDocument
{
    protected Invoice $invoice;

    protected Collection $documents;

    protected InvoiceDocument $xmlFile;

    protected InvoiceDocument $pdfFile;

    public function setInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->documents = $invoice->invoiceDocuments ?? new Collection;
        $this->xmlFile = $invoice->xmlInvoiceDocument ?? new InvoiceDocument;
        $this->pdfFile = $invoice->pdfInvoiceDocument ?? new InvoiceDocument;
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

    public function getXmlContent(): string
    {
        return self::obtainDocumentContent($this->xmlFile);
    }

    public function getXmlObject()
    {
        return self::xmlDocumentReadingConverter($this->xmlFile);
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
}

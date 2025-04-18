<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Create\Helpers\CommonMethodsForBuildersTrait;
use JiagBrody\LaravelFacturaMx\Sat\Create\Helpers\SaveCreateHelper;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

readonly class IngresoCreateBuilder
{
    use CommonMethodsForBuildersTrait;

    protected Invoice $invoice;

    protected SaveCreateHelper $saveIngreso;

    protected DocumentRepository $documentRepository;

    protected DocumentService $documentService;

    protected InvoiceDocument $xmlFile;

    public function __construct(
        // protected Credential           $credential,
        protected string $xmlContent,
        protected InvoiceCompanyHelper $companyHelper,
        protected AttributeAssembly $attributeAssembly
    ) {
        $this->saveIngreso = new SaveCreateHelper($this->attributeAssembly);
        $this->documentRepository = new DocumentRepository;
        $this->documentService = new DocumentService;
    }

    private function saveInvoice(): void
    {
        $this->invoice = $this->saveIngreso->createNewInvoice(companyHelperId: $this->companyHelper->id);
    }

    private function saveAdditionalTables(): void
    {
        // GUARDO TABLA DE COMPLEMENTO DE RELACIÓN
        $this->saveIngreso->upsertRelationshipsAddOn($this->invoice, $this->attributeAssembly->getCfdiRelacionados());

        // GUARDO TABLA INFORMACIÓN ADICIONAL PARA FACTURA INGRESO
        $this->saveIngreso->upsertAdditionalTables($this->invoice);

        // $this->saveIngreso->toInvoiceBalances($this->invoice);
        // $this->saveIngreso->toInvoiceTaxes($this->invoice);
        // $this->saveIngreso->ToComplementLocalTax($this->invoice, $this->attributeAssembly->getComplementoImpuestosLocales());
    }

    private function deleteAdditionalTables(): void
    {
        // DELETE "Complementos locales / Impuesto cedular"
        // if ($this->invoice->invoiceComplementLocalTax) {
        //     $this->invoice->invoiceComplementLocalTax->invoiceComplementLocalTaxDetails()->delete();
        //     $this->invoice->invoiceComplementLocalTax()->delete();
        // }

        // DELETE "Impuestos y sus detalles para retenciónes como traslados"
        // if ($this->invoice->invoiceTax) {
        //     $this->invoice->invoiceTax->invoiceTaxDetails()->delete();
        //     // $this->invoice->invoiceTax()->delete();
        // }

        $this->invoice->refresh();
    }

    public function saveInvoiceAndSaveDocuments(): Invoice
    {
        $this->saveInvoice();
        $this->saveAdditionalTables();
        $this->saveDocuments();

        $this->invoice->refresh();

        return $this->invoice;
    }

    public function reSaveDataAndRedoDocuments(Invoice $invoice): void
    {
        $this->invoice = $invoice;

        DB::transaction(function () {
            $this->deleteAdditionalTables();
            $this->deleteDocuments();
            $this->saveAdditionalTables();
            $this->saveDocuments();
        });
    }
}

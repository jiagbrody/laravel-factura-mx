<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use CfdiUtils\CfdiCreator40;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Helper\ConvertXmlContentToObjectHelper;
use JiagBrody\LaravelFacturaMx\Sat\Helper\GeneratePdfDocumentFromXmlObjectForIngresoHelper;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;
use PhpCfdi\Credentials\Credential;

readonly class IngresoCreateBuilder
{
    // MODELO ELOQUENT A RELACIONAR CON EL CLIENTE
    protected mixed $relationshipModel;

    protected Invoice $invoice;

    protected SaveIngreso $saveIngreso;

    protected InvoiceDocument $xmlFile;

    protected DocumentRepository $documentRepository;

    protected DocumentService $documentService;

    public function __construct(
        protected Credential $credential,
        protected CfdiCreator40 $creatorCfdi,
        protected InvoiceCompanyHelper $companyHelper,
        public AttributeAssembly $attributeAssembly
    ) {
        $this->saveIngreso = new SaveIngreso($this->attributeAssembly);
        $this->documentRepository = new DocumentRepository;
        $this->documentService = new DocumentService;
    }

    public function setRelationshipModel($model): self
    {
        $this->detectLogicError($model);
        $this->relationshipModel = $model;

        return $this;
    }

    public function getArrayOfXmlContentBeforeSaving(): array
    {
        return ConvertXmlContentToObjectHelper::make($this->creatorCfdi->asXml(), true);
    }

    public function getObjectOfXmlContentBeforeSaving(): object
    {
        return ConvertXmlContentToObjectHelper::make($this->creatorCfdi->asXml());
    }

    public function saveInvoice(): void
    {
        $this->invoice = $this->saveIngreso->toInvoice($this->relationshipModel->getMorphClass(), $this->relationshipModel->id, $this->companyHelper->id);
    }

    private function saveAdditionalTables(): void
    {
        // GUARDO TABLAS RELACIONADAS CON INFORMACIÓN PARA LA FACTURA DE INGRESO
        $this->saveIngreso->toInvoiceDetail($this->invoice);
        $this->saveIngreso->toInvoiceBalances($this->invoice);
        $this->saveIngreso->toInvoiceTaxes($this->invoice);
        $this->saveIngreso->ToComplementLocalTax($this->invoice, $this->attributeAssembly->getComplementoImpuestosLocales());

        // INICIALIZO PARAMETROS PARA USAR EL "DocumentService"
        $this->documentService->setInvoice($this->invoice);
    }

    public function saveDocuments(?string $fileName = null): void
    {
        $this->saveDocumentXml($fileName);
        $this->saveDocumentPdf($fileName);
    }

    private function saveDocumentXml($fileName): void
    {
        $this->xmlFile = $this->documentRepository->create(
            relationshipModel: $this->invoice->getMorphClass(),
            relationshipId: $this->invoice->id,
            documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
            fileName: $this->documentService->getFileNameToSave(),
            filePath: config('jiagbrody-laravel-factura-mx.invoices_files_path'),
            mimeType: 'xml',
            extension: 'xml',
            storage: config('jiagbrody-laravel-factura-mx.filesystem_disk'),
            fileContent: $this->creatorCfdi->asXml()
        );
    }

    private function saveDocumentPdf($fileName): void
    {
        $comprobante = $this->documentService->helpers::obtainXmlDocumentObject($this->xmlFile, associative: true);
        $documentPdf = (new GeneratePdfDocumentFromXmlObjectForIngresoHelper)(comprobante: $comprobante);
        $this->documentRepository->create(
            relationshipModel: $this->invoice->getMorphClass(),
            relationshipId: $this->invoice->id,
            documentTypeId: InvoiceDocumentTypeEnum::PDF_FILE->value,
            fileName: $this->documentService->getFileNameToSave(),
            filePath: config('jiagbrody-laravel-factura-mx.invoices_files_path'),
            mimeType: 'pdf',
            extension: 'pdf',
            storage: config('jiagbrody-laravel-factura-mx.filesystem_disk'),
            fileContent: $documentPdf
        );
    }

    private function deleteAdditionalTables(): void
    {
        // DELETE "Complementos locales / Impuesto cedular"
        if ($this->invoice->invoiceComplementLocalTax) {
            $this->invoice->invoiceComplementLocalTax->invoiceComplementLocalTaxDetails()->delete();
            $this->invoice->invoiceComplementLocalTax()->delete();
        }

        // DELETE "Impuestos y sus detalles para retenciónes como traslados"
        if ($this->invoice->invoiceTax) {
            $this->invoice->invoiceTax->invoiceTaxDetails()->delete();
            // $this->invoice->invoiceTax()->delete();
        }

        $this->invoice->refresh();
    }

    private function deleteDocuments(): void
    {
        $this->invoice->invoiceDocuments()->delete();
    }

    private function detectLogicError($model): void
    {
        if (! $model instanceof Model) {
            abort(422, 'La instancia no es Modelo Eloquent correcto.');
        }
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

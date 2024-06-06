<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use CfdiUtils\CfdiCreator40;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Document\DocumentHandler;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Sat\Stamp\StampInvoiceBuilder;
use PhpCfdi\Credentials\Credential;

readonly class IngresoCreateBuilder
{
    // MODELO ELOQUENT A RELACIONAR CON EL CLIENTE
    protected mixed $relationshipModel;

    protected Invoice $invoice;

    public StampInvoiceBuilder $pacProvider;

    protected DocumentHandler $documentHandler;

    public function __construct(
        protected Credential $credential,
        protected CfdiCreator40 $creatorCfdi,
        protected InvoiceCompanyHelper $companyHelper,
        protected AttributeAssembly $attributeAssembly
    ) {
        $this->documentHandler = new DocumentHandler;
    }

    public function setRelationshipModel($model): self
    {
        $this->detectLogicError($model);
        $this->relationshipModel = $model;

        return $this;
    }

    public function saveInvoice(): Invoice
    {
        $save = new SaveIngreso($this->attributeAssembly);

        $this->invoice = $save->toInvoice($this->relationshipModel->getMorphClass(), $this->relationshipModel->id, $this->companyHelper->id);
        $save->toInvoiceDetail($this->invoice);
        $save->toInvoiceBalances($this->invoice);
        $save->toInvoiceTaxes($this->invoice);

        // Declaro el proveedor del pac de acuerdo a los parámetros de configuración.
        // $this->pacProvider = (new StampInvoiceBuilder($this->invoice));

        return $this->invoice;
    }

    public function saveDocument(?string $fileName = null): void
    {
        $this->documentHandler->create(
            relationshipModel: $this->invoice->getMorphClass(),
            relationshipId: $this->invoice->id,
            documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
            fileName: $this->getFileName($fileName),
            filePath: config('factura-mx.invoices_files_path'),
            mimeType: 'xml',
            extension: 'xml',
            storage: config('factura-mx.filesystem_disk'),
            fileContent: $this->creatorCfdi->asXml()
        );
    }

    private function detectLogicError($model): void
    {
        if (! $model instanceof Model) {
            abort(422, 'La instancia no es Modelo Eloquent correcto.');
        }
    }

    private function getFileName(?string $fileName): string
    {
        if ($fileName === null) {
            $fileName = 'invoice-'.$this->invoice->id.'_'.Str::slug($this->attributeAssembly->getComprobanteAtributos()->getFecha());
        }

        return $fileName;
    }
}
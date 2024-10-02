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

    public StampInvoiceBuilder $stampBuilder;

    protected DocumentHandler $documentHandler;

    public function __construct(
        protected Credential $credential,
        protected CfdiCreator40 $creatorCfdi,
        protected InvoiceCompanyHelper $companyHelper,
        public AttributeAssembly $attributeAssembly
    ) {
        $this->documentHandler = new DocumentHandler;
    }

    public function setRelationshipModel($model): self
    {
        $this->detectLogicError($model);
        $this->relationshipModel = $model;

        return $this;
    }

    /*
     * Declaro el proveedor del pac de acuerdo a los parámetros de configuración (este podría ser dinámico).
     */
    // public function setPacProvider(): self
    // {
    //     $this->stampBuilder = ;
    //
    //     return $this;
    // }

    public function saveInvoice(): Invoice
    {
        $save = new SaveIngreso($this->attributeAssembly);

        $this->invoice = $save->toInvoice($this->relationshipModel->getMorphClass(), $this->relationshipModel->id, $this->companyHelper->id);

        $save->toInvoiceDetail($this->invoice);
        $save->toInvoiceBalances($this->invoice);
        $save->toInvoiceTaxes($this->invoice);
        $save->ToComplementLocalTax($this->invoice, $this->attributeAssembly->getComplementoImpuestosLocales());
        $save->toRelatedConcepts($this->invoice);

        return $this->invoice;
    }

    public function saveDocument(?string $fileName = null): void
    {
        $this->documentHandler->create(
            relationshipModel: $this->invoice->getMorphClass(),
            relationshipId: $this->invoice->id,
            documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
            fileName: $this->getFileName($fileName),
            filePath: config('jiagbrody-laravel-factura-mx.invoices_files_path'),
            mimeType: 'xml',
            extension: 'xml',
            storage: config('jiagbrody-laravel-factura-mx.filesystem_disk'),
            fileContent: $this->creatorCfdi->asXml()
        );
    }

    public function build(): array
    {
        $this->saveInvoice();

        $this->saveDocument();

        return [
            'invoice' => $this->invoice,
            'response' => (new StampInvoiceBuilder($this->invoice))->build(),
        ];
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

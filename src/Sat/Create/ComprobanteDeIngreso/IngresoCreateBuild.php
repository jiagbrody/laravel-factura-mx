<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use CfdiUtils\CfdiCreator40;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Document\SaveDocument;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PacProviderHelper;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\Stamp\StampBuild;
use PhpCfdi\Credentials\Credential;

class IngresoCreateBuild
{
    // MODELO ELOQUENT A RELACIONAR CON EL CLIENTE
    protected mixed $relationshipModel;

    protected Invoice $invoice;

    public FinkokPac $pacProvider;

    public function __construct(
        protected Credential           $credential,
        protected CfdiCreator40        $creatorCfdi,
        protected InvoiceCompanyHelper $companyHelper,
        protected AttributeAssembly    $attributeAssembly
    )
    {
    }

    public function setRelationshipModel($model): self
    {
        $this->detectLogicError($model);
        $this->relationshipModel = $model;
        return $this;
    }

    public function saveInvoice(): void
    {
        DB::transaction(function () {
            $save = new SaveIngreso($this->attributeAssembly);

            $this->invoice = $save->toInvoice($this->relationshipModel->getMorphClass(), $this->relationshipModel->id, $this->companyHelper->id);
            $save->toInvoiceDetails($this->invoice);
            $save->toInvoiceBalances($this->invoice);
            $save->toInvoiceTaxes($this->invoice);

            // Declaro el proveedor del pac de acuerdo a los parámetros de configuración.
            $this->pacProvider = (new PacProviderHelper())($this->invoice);
        });
    }

    public function saveDocument(null|string $fileName = null): void
    {
        if ($fileName === null) {
            $fileName = 'invoice-' . $this->invoice->id . '_' . Str::slug($this->attributeAssembly->getComprobanteAtributos()->getFecha());
        }

        (new SaveDocument(
            relationshipModel: $this->invoice->getMorphClass(),
            relationshipId: $this->invoice->id,
            documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
            fileName: $fileName,
            filePath: config('factura-mx.invoices_files_path'),
            mimeType: 'xml',
            extension: 'xml',
            storage: config('factura-mx.filesystem_disk'),
            fileContent: $this->creatorCfdi->asXml()
        ))->create();
    }

    public function makeStamp(): self
    {
        DB::transaction(function () {
            $stamp = new StampBuild($this->invoice);
            dd('test', $this);
        });

        return $this;
    }

    private function detectLogicError($model): void
    {
        if (!$model instanceof Model) {
            abort(422, 'La instancia no es Modelo Eloquent correcto.');
        }
    }
}

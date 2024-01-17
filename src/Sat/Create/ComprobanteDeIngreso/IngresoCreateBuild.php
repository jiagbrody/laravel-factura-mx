<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use CfdiUtils\CfdiCreator40;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Create\Includes\SaveDocument;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\Stamp\StampBuild;
use PhpCfdi\Credentials\Credential;

class IngresoCreateBuild
{
    protected $relationshipModel;

    protected $relationshipId;

    protected Invoice $invoice;

    protected FinkokPac $pacProvider;

    public function __construct(
        protected Credential $credential,
        protected CfdiCreator40 $creatorCfdi,
        protected InvoiceCompanyHelper $companyHelper,
        protected AttributeAssembly $attributeAssembly
    ) {
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
        });
    }

    public function saveDocument(?string $fileName = null): void
    {
        $save = new SaveDocument(
            relationshipModel: $this->relationshipModel->getMorphClass(),
            relationshipId: $this->relationshipModel->id,
            documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
            fileName: 'mi nombre de archivo',
            filePath: 'test',
            mimeType: 'xml',
            extension: 'xml',
            storage: 'public',
            fileContent: $this->creatorCfdi->asXml()
        );

        $save->make();
    }

    public function makeStamp(): self
    {
        $this->pacProvider = new FinkokPac($this->invoice);

        DB::transaction(function () {
            $stamp = new StampBuild($this->invoice);
        });

        return $this;
    }

    private function detectLogicError($model): void
    {
        if (! $model instanceof Model) {
            abort(422, 'La instancia no es Modelo Eloquent correcto.');
        }
    }
}

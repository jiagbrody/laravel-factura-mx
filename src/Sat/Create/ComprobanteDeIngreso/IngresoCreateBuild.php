<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use CfdiUtils\CfdiCreator40;
use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Sat\Stamp\StampBuild;
use PhpCfdi\Credentials\Credential;

class IngresoCreateBuild
{
    protected Invoice $invoice;

    protected FinkokPac $pacProvider;

    public function __construct(
        protected Credential $credential,
        protected CfdiCreator40 $creatorCfdi,
        protected InvoiceCompanyHelper $companyHelper,
        protected AttributeAssembly $attributeAssembly
    ) {
    }

    public function saveInvoice(string $relationshipModel, int $relationshipId): self
    {
        DB::transaction(function () use ($relationshipModel, $relationshipId) {
            $save = new SaveIngreso($this->attributeAssembly);

            $this->invoice = $save->toInvoice($relationshipModel, $relationshipId, $this->companyHelper->id);
            $save->toInvoiceDetails($this->invoice);
            $save->toInvoiceBalances($this->invoice);
            $save->toInvoiceTaxes($this->invoice);
        });

        return $this;
    }

    public function makeStamp(): self
    {
        $this->pacProvider = new FinkokPac($this->invoice);
        dd($this->pacProvider);
        DB::transaction(function () {
            $stamp = new StampBuild($this->invoice);
            dd($this->pacProvider);
        });

        return $this;
    }

    public function tools()
    {
    }
}

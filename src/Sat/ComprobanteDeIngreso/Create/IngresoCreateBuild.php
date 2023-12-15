<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Create;

use CfdiUtils\CfdiCreator40;
use Illuminate\Support\Facades\DB;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\SaveIngreso;
use JiagBrody\LaravelFacturaMx\Sat\Helper\DraftBuild;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use PhpCfdi\Credentials\Credential;

class IngresoCreateBuild extends DraftBuild
{
    protected Invoice $invoice;

    public function __construct(protected Credential $credential, protected CfdiCreator40 $creatorCfdi, protected InvoiceCompanyHelper $companyHelper, protected AttributeAssembly $attributeAssembly)
    {
    }

    public function saveInvoice(string $relationshipModel, int $relationshipId): Invoice
    {
        return DB::transaction(function () use ($relationshipModel, $relationshipId) {
            $save = new SaveIngreso($this->attributeAssembly);

            $invoice = $save->toInvoice($relationshipModel, $relationshipId, $this->companyHelper->id);
            $save->toInvoiceDetails($invoice);
            $save->toInvoiceBalances($invoice);
            $save->toInvoiceTaxes($invoice);

            return $invoice;
        });
    }
}

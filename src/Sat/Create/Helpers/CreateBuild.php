<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;

// use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

class CreateBuild
{
    use CommonMethodsForBuildersTrait;

    // protected DocumentService $documentService;

    public function __construct(
        protected string $xmlContent,
        protected InvoiceCompanyHelper $companyHelper,
        protected AttributeAssembly $attributeAssembly
    ) {
        // $this->documentService = new DocumentService;
    }

    public function createNewInvoice(): Invoice
    {
        return (new SaveCreateHelper(attributeAssembly: $this->attributeAssembly))->createInvoice($this->companyHelper->id);
    }

    public function deleteDocuments($invoice): void
    {
        $invoice->invoiceDocuments()->delete();
    }
}

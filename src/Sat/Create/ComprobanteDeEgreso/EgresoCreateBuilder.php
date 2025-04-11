<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso;

use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Create\Helpers\CommonMethodsForBuildersTrait;
use JiagBrody\LaravelFacturaMx\Sat\Create\Helpers\SaveCreateTrait;
use JiagBrody\LaravelFacturaMx\Sat\Helper\DraftBuild;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

class EgresoCreateBuilder
{
    use CommonMethodsForBuildersTrait, SaveCreateTrait;

    protected DocumentRepository $documentRepository;

    protected DocumentService $documentService;

    public function __construct(
        protected string               $xmlContent,
        protected InvoiceCompanyHelper $companyHelper,
        protected AttributeAssembly    $attributeAssembly
    )
    {
        $this->documentRepository = new DocumentRepository;
        $this->documentService = new DocumentService;
        dd('test');
    }


}

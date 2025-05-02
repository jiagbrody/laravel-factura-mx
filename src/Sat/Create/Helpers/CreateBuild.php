<?php

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Helpers;

use JiagBrody\LaravelFacturaMx\Enums\InvoiceDocumentTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument\DocumentRepository;
use JiagBrody\LaravelFacturaMx\Sat\AttributeAssembly;
use JiagBrody\LaravelFacturaMx\Sat\Helper\ConvertXmlContentToObjectHelper;
use JiagBrody\LaravelFacturaMx\Sat\Helper\GeneratePdfDocumentFromXmlObjectForIngresoHelper;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceCompanyHelper;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;

class CreateBuild
{
    use CommonMethodsForBuildersTrait;

    protected SaveCreateHelper $saveCreateHelper;

    protected DocumentRepository $documentRepository;

    protected DocumentService $documentService;

    public function __construct(
        protected string $xmlContent,
        protected InvoiceCompanyHelper $companyHelper,
        protected AttributeAssembly $attributeAssembly
    ) {
        $this->saveCreateHelper = new SaveCreateHelper(attributeAssembly: $attributeAssembly);
        $this->documentRepository = new DocumentRepository;
        $this->documentService = new DocumentService;
    }

    public function getAttributeAssembly(): AttributeAssembly
    {
        return $this->attributeAssembly;
    }

    public function createNewInvoice(): Invoice
    {
        $invoice = $this->saveCreateHelper->createInvoice($this->companyHelper->id);

        return $invoice;
    }

    // public function saveAdditionalTables(Invoice $invoice): void
    // {
    //     $this->saveCreateHelper->saveIncome($invoice);
    //     $this->saveCreateHelper->saveRelationshipsAddOn($invoice);
    // }

    public function deleteDocuments($invoice): void
    {
        $invoice->invoiceDocuments()->delete();
    }

    public function createXmlDocument(Invoice $invoice, bool $alsoCreatePdf)
    {
        $this->documentService->setInvoice($invoice);

        $this->saveDocumentXml($invoice);

        if ($alsoCreatePdf) {
            $this->saveDocumentPdf($invoice);
        }

    }

    private function saveDocumentXml(Invoice $invoice, string $fileName = ''): \JiagBrody\LaravelFacturaMx\Models\InvoiceDocument
    {
        return $this->documentRepository->create(
            relationshipModel: $invoice->getMorphClass(),
            relationshipId: $invoice->id,
            documentTypeId: InvoiceDocumentTypeEnum::XML_FILE->value,
            fileName: $this->documentService->getFileNameToSave($fileName),
            filePath: config('jiagbrody-laravel-factura-mx.invoices_files_path'),
            mimeType: 'xml',
            extension: 'xml',
            storage: config('jiagbrody-laravel-factura-mx.filesystem_disk'),
            fileContent: $this->xmlContent
        );
    }

    private function saveDocumentPdf(Invoice $invoice, $fileName = ''): void
    {
        $comprobante = ConvertXmlContentToObjectHelper::make($this->xmlContent, true);
        $documentPdf = (new GeneratePdfDocumentFromXmlObjectForIngresoHelper)(comprobante: $comprobante);

        $this->documentRepository->create(
            relationshipModel: $invoice->getMorphClass(),
            relationshipId: $invoice->id,
            documentTypeId: InvoiceDocumentTypeEnum::PDF_FILE->value,
            fileName: $this->documentService->getFileNameToSave(),
            filePath: config('jiagbrody-laravel-factura-mx.invoices_files_path'),
            mimeType: 'pdf',
            extension: 'pdf',
            storage: config('jiagbrody-laravel-factura-mx.filesystem_disk'),
            fileContent: $documentPdf
        );
    }
}

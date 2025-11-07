<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument;

use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;

class DocumentRepository implements DocumentRepositoryInterface
{
    /**
     * @throws \Exception
     */
    public function create(
        string $relationshipModel,
        int $relationshipId,
        $documentTypeId,
        string $fileName,
        string $filePath,
        string $mimeType,
        string $extension,
        string $storage,
        string $fileContent
    ): InvoiceDocument {
        return (new CreateDocument(
            relationshipModel: $relationshipModel,
            relationshipId: $relationshipId,
            documentTypeId: $documentTypeId,
            fileName: $fileName,
            filePath: $filePath,
            mimeType: $mimeType,
            extension: $extension,
            storage: $storage,
            fileContent: $fileContent
        ))();
    }

    /**
     * @throws \Exception
     */
    public function update(
        InvoiceDocument $invoiceDocument,
        string $relationshipModel,
        int $relationshipId,
        $documentTypeId,
        string $fileName,
        string $filePath,
        string $mimeType,
        string $extension,
        string $storage,
        string $fileContent,
        bool $overwriteFileOnDisk = false
    ): void {
        (new UpdateDocument(
            invoiceDocument: $invoiceDocument,
            overwriteFileOnDisk: $overwriteFileOnDisk
        ))(
            documentTypeId: $documentTypeId,
            fileName: $fileName,
            filePath: $filePath,
            mimeType: $mimeType,
            extension: $extension,
            storage: $storage,
            relationshipModel: $relationshipModel,
            relationshipId: $relationshipId,
            fileContent: $fileContent
        );
    }

    public function delete(InvoiceDocument $invoiceDocument): void
    {
        (new DeleteDocument)(invoiceDocument: $invoiceDocument);
    }
}

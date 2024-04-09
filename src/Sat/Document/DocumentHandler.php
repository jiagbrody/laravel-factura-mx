<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Document;

class DocumentHandler implements DocumentHandlerInterface
{
    public function create(
        string $relationshipModel,
        int    $relationshipId,
               $documentTypeId,
        string $fileName,
        string $filePath,
        string $mimeType,
        string $extension,
        string $storage,
        string $fileContent
    ): void
    {
        (new CreateDocument(
            relationshipModel: $relationshipModel,
            relationshipId: $relationshipId,
            documentTypeId: $documentTypeId,
            fileName: $fileName,
            filePath: $filePath,
            mimeType: $mimeType,
            extension: $extension,
            storage: $storage,
            fileContent: $fileContent
        ))->create();
    }

    public function update(
        string $relationshipModel,
        int    $relationshipId,
        string $fileName,
        string $fileContent
    ): void
    {
        (new UpdateDocument(
            relationshipModel: $relationshipModel,
            relationshipId: $relationshipId,
            fileName: $fileName,
            fileContent: $fileContent
        ))->update();
    }
}

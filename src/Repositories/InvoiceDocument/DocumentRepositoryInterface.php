<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument;

use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;

interface DocumentRepositoryInterface
{
    /*
     * Crea nuevo registro y archivo en el disco
     */
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
    ): InvoiceDocument;

    /*
     * Actualiza el registro y sobre escritura del archivo. Si es una nueva ubicación se genera y se borra el antiguo.
     */
    public function update(
        InvoiceDocument $invoiceDocument,
        string          $relationshipModel,
        int             $relationshipId,
                        $documentTypeId,
        string          $fileName,
        string          $filePath,
        string          $mimeType,
        string          $extension,
        string          $storage,
        string          $fileContent
    );

    /*
     * Borra el registro y el archivo en el disco.
     */
    public function delete(InvoiceDocument $invoiceDocument);
}

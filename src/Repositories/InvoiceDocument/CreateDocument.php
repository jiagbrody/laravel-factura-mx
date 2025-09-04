<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument;

use Illuminate\Support\Facades\Storage;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;

final class CreateDocument
{
    private ?InvoiceDocument $exists;

    public function __construct(
        protected string $relationshipModel,
        protected int    $relationshipId,
        protected        $documentTypeId,
        protected string $fileName,
        protected string $filePath,
        protected string $mimeType,
        protected string $extension,
        protected string $storage,
        protected string $fileContent
    )
    {
        $this->exists = InvoiceDocument::where([
            ['documentable_type', $this->relationshipModel],
            ['documentable_id', $this->relationshipId],
            ['invoice_document_type_id', $this->documentTypeId],
            ['file_name', $this->fileName],
            ['file_path', $this->filePath],
            ['mime_type', $this->mimeType],
            ['extension', $this->extension],
            ['storage', $this->storage],
        ])->first();
    }

    /**
     * @throws \Exception
     */
    public function __invoke(): InvoiceDocument
    {
        // NOTA: SI EXISTE UN ARCHIVO CON EL MISMO NOMBRE, MODELO Y ID SIMPLEMENTE REGENERO EL ARCHIVO SIN GUARDAR OTRO REGISTRO EN LA BASE DE DATOS.
        if ($this->exists) {
            Storage::disk($this->exists->storage)->put($this->exists->file, $this->fileContent);

            return $this->exists;
        }

        $archive = $this->filePath . '/' . $this->fileName . '.' . $this->extension;

        // Save the file to disk and verify the result.
        // If Storage::put() returns `false`, we throw an exception.
        if (!Storage::disk($this->storage)->put($archive, $this->fileContent)) {
            // This exception will cause the transaction to roll back.
            throw new \Exception('Error saving file. File of the own billing library "CreateDocument.php". on action Storage::put()');
        }

        return $this->saveDocumentInstance();
    }

    private function saveDocumentInstance(): InvoiceDocument
    {
        $document = new InvoiceDocument;

        $document->invoice_document_type_id = $this->documentTypeId;
        $document->file_name = $this->fileName;
        $document->file_path = $this->filePath;
        $document->mime_type = $this->mimeType;
        $document->extension = $this->extension;
        $document->storage = $this->storage;
        $document->documentable_type = $this->relationshipModel;
        $document->documentable_id = $this->relationshipId;
        $document->save();

        return $document;
    }
}

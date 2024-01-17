<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\Includes;

use Exception;
use Illuminate\Support\Facades\Storage;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;

final class SaveDocument implements SaveDocumentInterface
{
    private ?InvoiceDocument $exists;

    public function __construct(
        protected string $relationshipModel,
        protected int $relationshipId,
        protected $documentTypeId,
        protected string $fileName,
        protected string $filePath,
        protected string $mimeType,
        protected string $extension,
        protected string $storage,
        protected string $fileContent
    )
    {
        date_default_timezone_set(config('factura-mx.default_timezone'));
        $this->exists = InvoiceDocument::where([
            ['documentable_type', $this->relationshipModel],
            ['documentable_id', $this->relationshipId],
            ['file_name', $this->fileName],
            ['file_path', $this->filePath],
            ['mime_type', $this->mimeType],
            ['extension', $this->extension],
            ['storage', $this->storage],
        ])->first();
    }

    public function make(): InvoiceDocument
    {
        //NOTA: SI EXISTE UN ARCHIVO CON EL MISMO NOMBRE, MODELO Y ID SIMPLEMENTE REGENERO EL ARCHIVO SIN GUARDAR OTRO REGISTRO EN LA BASE DE DATOS.
        if ($this->exists) {
            Storage::disk($this->exists->storage)->put($this->exists->file, $this->fileContent);

            return $this->exists;
        }

        $archive         = $this->filePath . '/' . $this->fileName . '.' . $this->extension;
        $documentCreated = new InvoiceDocument;


        if (Storage::disk($this->storage)->put($archive, $this->fileContent)) {
            try {
                $documentCreated = $this->saveDocumentInstance();
            } catch (Exception $e) {
                Storage::disk($this->storage)->delete($archive);
                abort(403, 'Error al generar el archivo: ' . $e->getMessage());
            }
        }

        return $documentCreated;
    }

    private function saveDocumentInstance(): ?InvoiceDocument
    {
        $document = new InvoiceDocument;

        $document->document_type_id  = $this->documentTypeId;
        $document->documentable_type = $this->relationshipModel;
        $document->documentable_id   = $this->relationshipId;
        $document->file_name         = $this->fileName;
        $document->file_path         = $this->filePath;
        $document->mime_type         = $this->mimeType;
        $document->extension         = $this->extension;
        $document->storage           = $this->storage;
        $document->save();

        return $document;
    }
}

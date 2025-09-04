<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument;

use Illuminate\Support\Facades\Storage;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;

final readonly class UpdateDocument
{
    public function __construct(private InvoiceDocument $invoiceDocument, private bool $overwriteFileOnDisk = false)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(
        int    $documentTypeId,
        string $fileName,
        string $filePath,
        string $mimeType,
        string $extension,
        string $storage,
        string $relationshipModel,
        int    $relationshipId,
        string $fileContent
    ): void
    {
        // $this->invoiceDocument->file_name = config('jiagbrody-laravel-factura-mx.prefix_for_stamped_files') . ((empty($this->fileName)) ? $this->invoiceDocument->file_name : $this->fileName);

        // GUARDO PRIMERO EL ARCHIVO EN EL DISCO SI YA EXISTE LO REEMPLAZO SINO BORRO EL ANTERIOR Y AGREGO EL NUEVO
        $this->upsertStorageFile($fileContent);

        $this->invoiceDocument->invoice_document_type_id = $documentTypeId;
        $this->invoiceDocument->file_name = $fileName;
        $this->invoiceDocument->file_path = $filePath;
        $this->invoiceDocument->mime_type = $mimeType;
        $this->invoiceDocument->extension = $extension;
        $this->invoiceDocument->storage = $storage;
        $this->invoiceDocument->documentable->type = $relationshipModel;
        $this->invoiceDocument->documentable_id = $relationshipId;
        $this->invoiceDocument->save();
    }

    /**
     * @throws \Exception
     */
    private function upsertStorageFile($fileContent): void
    {
        // NOTA: SI EXISTE UN ARCHIVO CON EL MISMO NOMBRE Y NO TIENE INSTRUCCIÓN DE SOBREESCRIBIR, MARCA ERROR.
        if (Storage::disk($this->invoiceDocument->storage)->exists($this->invoiceDocument->file) && $this->overwriteFileOnDisk === false) {
            abort('403', 'The file already exists and the instruction is not to overwrite it.');
        }

        // Guardar el archivo en el disco y verificar el resultado.
        // Si Storage::put() retorna `false`, lanzamos una excepción
        if (!Storage::disk($this->invoiceDocument->storage)->put($this->invoiceDocument->file, $fileContent)) {
            // Esta excepción hará que la transacción se revierta.
            throw new \Exception('Error saving file. Invoice creation library "UpdateDocument.php"');
        }
    }
}

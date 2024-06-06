<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Document;

use Illuminate\Support\Facades\Storage;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;

class UpdateDocument
{
    private InvoiceDocument $invoiceDocument;

    private string $oldFullFileName;

    public function __construct(
        protected string $relationshipModel,
        protected int $relationshipId,
        protected string $fileName,
        protected string $fileContent
    ) {
        $this->invoiceDocument = InvoiceDocument::whereDocumentableType($relationshipModel)->whereDocumentableId($relationshipId)->firstOrFail();
        $this->oldFullFileName = $this->invoiceDocument->getFileAttribute();
    }

    public function update(): InvoiceDocument
    {
        $this->invoiceDocument->file_name = config('jiagbrody-laravel-factura-mx.prefix_for_stamped_files').((empty($this->fileName)) ? $this->invoiceDocument->file_name : $this->fileName);
        $this->invoiceDocument->save();

        $this->overwriteFileDocument();

        return $this->invoiceDocument;
    }

    private function overwriteFileDocument(): void
    {

        if (Storage::disk($this->invoiceDocument->storage)->put($this->invoiceDocument->getFileAttribute(), $this->fileContent)) {
            Storage::disk($this->invoiceDocument->storage)->delete($this->oldFullFileName);
        }
    }
}

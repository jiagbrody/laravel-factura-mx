<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Repositories\InvoiceDocument;

use Illuminate\Support\Facades\Storage;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;

final class DeleteDocument
{
    public function __invoke(InvoiceDocument $invoiceDocument): void
    {
        if (Storage::disk($invoiceDocument->storage)->exists($invoiceDocument->file)) {
            Storage::disk($invoiceDocument->storage)->delete($invoiceDocument->file);
        }

        $invoiceDocument->delete();
    }
}

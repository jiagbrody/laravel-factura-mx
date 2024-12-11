<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use Illuminate\Support\Facades\Storage;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;

trait DocumentHelpersTrait
{
    /*
     * Obtiene el documento en lectura (string) para trabajarlo.
     */
    public static function obtainExistingDocumentFile(InvoiceDocument $document): ?string
    {
        if (Storage::disk($document->storage)->exists($document->file)) {
            return Storage::disk($document->storage)->get($document->file);
        }

        return null;
    }

    /*
     * Leer el XML en formato objeto. Por ejemplo para mostrar en frontend o tratar datos para el backend.
     */
    public static function obtainXmlDocumentObject(InvoiceDocument $document, $associative = null, $depth = 512, $flags = 0)
    {
        $contents = self::obtainExistingDocumentFile($document);

        if ($contents === null) {
            return false;
        }

        return ConvertXmlContentToObjectHelper::make($contents, $associative);
    }

    public static function obtainXmlDocumentArray(InvoiceDocument $document, $associative = null, $depth = 512, $flags = 0)
    {
        $contents = self::obtainExistingDocumentFile($document);

        if ($contents === null) {
            return false;
        }

        return ConvertXmlContentToObjectHelper::make($contents, $associative);
    }
}

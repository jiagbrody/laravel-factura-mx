<?php

namespace JiagBrody\LaravelFacturaMx\Enums;

enum InvoiceDocumentTypeEnum: int
{
    case XML_FILE = 1;
    case PDF_FILE = 2;

    public function getName(): string
    {
        return match ($this) {
            self::XML_FILE => 'Archivo XML',
            self::PDF_FILE => 'Archivo PDF',
        };
    }

    public function getMimeType(): string
    {
        return match ($this) {
            self::XML_FILE => 'xml',
            self::PDF_FILE => 'pdf',
        };
    }

    public function getExtension(): string
    {
        return match ($this) {
            self::XML_FILE => 'xml',
            self::PDF_FILE => 'pdf',
        };
    }
}

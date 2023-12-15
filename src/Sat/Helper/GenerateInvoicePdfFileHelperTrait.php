<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use App\Enums\InvoiceCfdiTypeEnum;
use App\Helpers\Cfdi\ConvertXmlContentToObjectHelper;
use App\Helpers\ConvertNumberToReadableTextHelper;
use App\Models\Document;
use App\Services\Documentable\DocumentDestroyService;
use App\Services\Documentable\DocumentGenerateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

trait GenerateInvoicePdfFileHelperTrait
{
    protected function createPdfFile(Document $xmlDocument, $xmlContent): Document
    {
        $comprobante = ConvertXmlContentToObjectHelper::make($xmlContent, true);

        switch ($this->invoice->invoice_cfdi_type_id) {
            case InvoiceCfdiTypeEnum::INGRESO->value:
                $readableText = ConvertNumberToReadableTextHelper::make(number_format((float) $comprobante['Total'], 2,
                    '.', ''), 'pesos', 'con', 'centavos');
                $episode = null;
                $statement = null;
                $pdfFile = PDF::loadView('pdf.invoices.invoice_ingreso',
                    compact('comprobante', 'episode', 'statement', 'readableText'));
                break;
            case InvoiceCfdiTypeEnum::PAGO->value:
                $readableText = ConvertNumberToReadableTextHelper::make(number_format((float) $comprobante['Total'], 2,
                    '.', ''), 'pesos', 'con', 'centavos');
                $pdfFile = PDF::loadView('pdf.invoices.invoice_ingreso', compact('comprobante', 'readableText'));
                break;
            default:
                $pdfFile = null;
        }

        try {
            return (new DocumentGenerateService(model: $xmlDocument->documentable, path: $xmlDocument->file_path,
                fileName: $xmlDocument->file_name.'.pdf', extension: 'pdf', mimeType: 'text/pdf',
                fileContent: $pdfFile->output()))->make();
        } catch (Exception $e) {
            (new DocumentDestroyService($xmlDocument))->make(); // Destruyo el XML generado. Ya que no se guardará nada en la base de datos.
            abort(422, 'Error al generar documento PDF: '.$e->getMessage());
        }
    }
}

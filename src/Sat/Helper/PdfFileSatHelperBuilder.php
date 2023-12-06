<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use App\Enums\InvoiceCfdiTypeEnum;
use App\Helpers\Cfdi\ConvertXmlContentToObjectHelper;
use App\Helpers\ConvertNumberToReadableTextHelper;
use App\Models\Document;
use App\Services\Documentable\DocumentDestroyService;
use App\Services\Documentable\DocumentGenerateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

final class PdfFileSatHelperBuilder
{
    private InvoiceCfdiTypeEnum $invoiceCfdiTypeEnum;

    private string $xmlContent;

    private Document $xmlDocument;

    private ?\Barryvdh\DomPDF\PDF $pdfFile;

    public function setInvoiceCfdiType(int $id): self
    {
        $this->invoiceCfdiTypeEnum = InvoiceCfdiTypeEnum::from($id);
        return $this;
    }

    public function setXmlDocument($xmlDocument): self
    {
        $this->xmlDocument = $xmlDocument;
        return $this;
    }

    public function setXmlContent($xmlContent): self
    {
        $this->xmlContent = $xmlContent;
        return $this;
    }

    public function build(): Document
    {
        $this->createContentPdf();
        return $this->generatePdf();
    }

    private function createContentPdf(): void
    {
        $comprobante = ConvertXmlContentToObjectHelper::make($this->xmlContent, true);

        switch ($this->invoiceCfdiTypeEnum->value) {
            case InvoiceCfdiTypeEnum::INGRESO->value:
            case InvoiceCfdiTypeEnum::EGRESO->value:
                $readableText = ConvertNumberToReadableTextHelper::make(number_format((float)$comprobante['Total'], 2, '.', ''), "pesos", "con", "centavos");
                $episode      = null;
                $statement    = null;
                $pdfFile      = PDF::loadView('pdf.invoices.invoice_ingreso', compact('comprobante', 'episode', 'statement', 'readableText'));
                break;
            case InvoiceCfdiTypeEnum::PAGO->value:
                $readableText = null;
                $episode      = null;
                $statement    = null;
                $pdfFile      = PDF::loadView('pdf.invoices.invoice_ingreso', compact('comprobante', 'episode', 'statement', 'readableText'));
                break;
            default:
                $pdfFile = null;
        }
        $this->pdfFile = $pdfFile;
    }

    private function generatePdf()
    {
        try {
            return (new DocumentGenerateService(model: $this->xmlDocument->documentable, path: $this->xmlDocument->file_path, fileName: $this->xmlDocument->file_name . '.pdf', extension: 'pdf', mimeType: 'text/pdf', fileContent: $this->pdfFile->output()))->make();
        } catch (Exception $e) {
            (new DocumentDestroyService($this->xmlDocument))->make(); # Destruyo el XML generado. Ya que no se guardará nada en la base de datos.
            abort(422, "Error al generar documento PDF: " . $e->getMessage());
        }
    }
}

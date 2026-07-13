<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;

final class GeneratePdfDocumentFromXmlObjectForIngresoHelper
{
    public function __invoke(array $comprobante): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(140),
            new SvgImageBackEnd
        );
        $qrCode = (new Writer($renderer))->writeString($this->buildSatVerificationUrl($comprobante), 'UTF-8');

        $episode = null;
        $statement = null;
        $readableText = (new ConvertNumberToReadableTextHelper)(
            amount: $comprobante['Total'],
            currencyLabel: 'pesos',
            separatorLabel: 'con',
            decimalLabel: 'centavos'
        );

        // Siempre se usa la vista genérica del paquete. Para personalizarla,
        // publícala en resources/views/vendor/jiagbrody-laravel-factura-mx/
        // respetando este contrato de variables: comprobante, episode,
        // statement, readableText, qrCode. Si el app anfitrión genera su
        // propio PDF (con vista y datos propios), desactiva el del paquete
        // con generate_pdf_on_stamp = false.
        $pdfFile = Pdf::loadView('jiagbrody-laravel-factura-mx::pdf.invoices.invoice_ingreso', compact('comprobante', 'episode', 'statement', 'readableText', 'qrCode'));

        return $pdfFile->output();
    }

    /*
     * URL oficial de verificación del CFDI (Anexo 20). El QR del PDF debe
     * apuntar aquí: id = UUID del timbre, re/rr = RFC emisor/receptor,
     * tt = total del comprobante, fe = últimos 8 caracteres del Sello del
     * emisor (Comprobante@Sello).
     */
    private function buildSatVerificationUrl(array $comprobante): string
    {
        $sello = (string) ($comprobante['Sello'] ?? '');

        return 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?'.http_build_query([
            'id' => $comprobante['Complemento']['TimbreFiscalDigital']['UUID'] ?? '',
            're' => $comprobante['Emisor']['Rfc'] ?? '',
            'rr' => $comprobante['Receptor']['Rfc'] ?? '',
            'tt' => $comprobante['Total'] ?? '',
            'fe' => substr($sello, -8),
        ]);
    }
}

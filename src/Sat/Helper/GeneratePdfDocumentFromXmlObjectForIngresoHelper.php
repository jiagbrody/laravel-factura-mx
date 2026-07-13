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

        // Si el app anfitrión tiene su propia copia de la vista (versiones
        // previas del paquete pedían copiarla a resources/views), se respeta;
        // de lo contrario se usa la vista incluida en el paquete. Para
        // personalizarla, publícala en resources/views/vendor/jiagbrody-laravel-factura-mx.
        $viewName = view()->exists('jiagbrody-laravel-factura-mx.pdf.invoices.invoice_ingreso')
            ? 'jiagbrody-laravel-factura-mx.pdf.invoices.invoice_ingreso'
            : 'jiagbrody-laravel-factura-mx::pdf.invoices.invoice_ingreso';

        $pdfFile = Pdf::loadView($viewName, compact('comprobante', 'episode', 'statement', 'readableText', 'qrCode'));

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

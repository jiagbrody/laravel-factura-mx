<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;

// use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
// use BaconQrCode\Renderer\GDLibRenderer;
// use BaconQrCode\Writer;

final class GeneratePdfDocumentFromXmlObjectForIngresoHelper
{
    public function __invoke(array $comprobante): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(140),
            new SvgImageBackEnd
        );
        $qrCode = (new Writer($renderer))->writeString('google.com', 'UTF-8');
        // $writer->writeFile('Hello World!', 'qrcode.svg');

        // $renderer = new GDLibRenderer(400);
        // $writer = new Writer($renderer);
        // $writer->writeFile('Hello World!', 'qrcode.png');

        $episode = null;
        $statement = null;
        $readableText = (new ConvertNumberToReadableTextHelper)(
            amount: $comprobante['Total'],
            currencyLabel: 'pesos',
            separatorLabel: 'con',
            decimalLabel: 'centavos'
        );
        $pdfFile = PDF::loadView('jiagbrody-laravel-factura-mx.pdf.invoices.invoice_ingreso',
            compact('comprobante', 'episode', 'statement', 'readableText', 'qrCode'));

        return $pdfFile->output();
    }

    private function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}

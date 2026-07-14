<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use JiagBrody\LaravelFacturaMx\Exceptions\InvoiceAlreadyStampedException;
use JiagBrody\LaravelFacturaMx\Exceptions\InvoiceDocumentMissingException;
use JiagBrody\LaravelFacturaMx\Exceptions\PacStampInProgressException;
use JiagBrody\LaravelFacturaMx\Exceptions\StaleCfdiDraftException;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;
use JiagBrody\LaravelFacturaMx\Models\InvoiceIncident;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Tests\Support\FakeSoapCaller;

/*
 * Tests de contrato contra respuestas REALES del PAC Finkok, grabadas de los
 * logs SOAP (julio 2026). Fijan el comportamiento ante cada variante conocida:
 * éxito directo, timbrado previo (307), timbrado asíncrono, incidencias y
 * recuperación de XML.
 */

const REAL_UUID = '9D525202-7069-5A3A-94CA-01BF2CD8EDA9';

function makeStampablePac(?string $draftXml = null): array
{
    config()->set('jiagbrody-laravel-factura-mx.stamped_poll_delay_seconds', 0);
    Storage::fake('local');

    $company = InvoiceCompany::factory()->create();
    $invoice = Invoice::factory()->create(['invoice_company_id' => $company->id]);

    $draftXml ??= freshDraftXml();
    Storage::disk('local')->put('drafts/invoice-'.$invoice->id.'-draft.xml', $draftXml);

    InvoiceDocument::create([
        'invoice_document_type_id' => 1, // XML
        'documentable_type' => $invoice->getMorphClass(),
        'documentable_id' => $invoice->id,
        'file_name' => 'invoice-'.$invoice->id.'-draft',
        'file_path' => 'drafts',
        'mime_type' => 'text/xml',
        'extension' => 'xml',
        'storage' => 'local',
    ]);

    $pac = new FinkokPac($invoice);
    $pac->setInvoiceCompanyHelper($company);

    return [$pac, $invoice];
}

function freshDraftXml(?string $fecha = null): string
{
    $fecha ??= (new DateTimeImmutable('now', new DateTimeZone('America/Mexico_City')))->format('Y-m-d\TH:i:s');

    return '<?xml version="1.0" encoding="UTF-8"?><cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/4" Version="4.0" Fecha="'.$fecha.'" Sello="AAA" Total="612.90"/>';
}

function stampSuccessResult(): stdClass
{
    return json_decode(json_encode(['stampResult' => [
        'UUID' => REAL_UUID,
        'Fecha' => '2026-07-13T13:25:36',
        'CodEstatus' => 'Comprobante timbrado satisfactoriamente',
        'xml' => '<cfdi:Comprobante>timbrado</cfdi:Comprobante>',
        'Incidencias' => new stdClass,
    ]]));
}

/*
 * Grabada del demo Finkok: el campo UUID trae el WorkProcessId, NO el UUID
 * fiscal, y no incluye el XML.
 */
function stampPreviouslyStampedResult(): stdClass
{
    return json_decode(json_encode(['stampResult' => [
        'UUID' => REAL_UUID,
        'Fecha' => '2026-07-13T13:25:36',
        'CodEstatus' => 'Comprobante timbrado previamente',
        'Incidencias' => ['Incidencia' => [
            'IdIncidencia' => 'ID_incidencia',
            'CodigoError' => '307',
            'WorkProcessId' => REAL_UUID,
            'MensajeIncidencia' => 'El CFDI contiene un timbre previo',
            'ExtraInfo' => '',
        ]],
    ]]));
}

/*
 * Grabada del demo Finkok: "stamped" con timbrado completado responde
 * "Comprobante recibido satisfactoriamente" CON el XML completo.
 */
function stampedCompletedResult(): stdClass
{
    return json_decode(json_encode(['stampedResult' => [
        'xml' => '<cfdi:Comprobante>timbrado con TFD UUID '.REAL_UUID.'</cfdi:Comprobante>',
        'UUID' => REAL_UUID,
        'Fecha' => '2026-07-13 13:25:36',
        'CodEstatus' => 'Comprobante recibido satisfactoriamente',
        'Incidencias' => new stdClass,
    ]]));
}

function stampedPendingResult(): stdClass
{
    return json_decode(json_encode(['stampedResult' => [
        'UUID' => REAL_UUID,
        'CodEstatus' => 'Comprobante recibido satisfactoriamente',
        'Incidencias' => new stdClass,
    ]]));
}

function stampWithSingleIncidenceResult(): stdClass
{
    return json_decode(json_encode(['stampResult' => [
        'CodEstatus' => '',
        'Incidencias' => ['Incidencia' => [
            'IdIncidencia' => 'abc-123',
            'CodigoError' => '301',
            'MensajeIncidencia' => 'XML mal formado',
            'ExtraInfo' => 'Elemento Concepto inválido',
        ]],
    ]]));
}

function stampWithMultipleIncidencesResult(): stdClass
{
    return json_decode(json_encode(['stampResult' => [
        'CodEstatus' => '',
        'Incidencias' => ['Incidencia' => [
            ['IdIncidencia' => 'a-1', 'CodigoError' => '301', 'MensajeIncidencia' => 'XML mal formado'],
            ['IdIncidencia' => 'a-2', 'CodigoError' => '401', 'MensajeIncidencia' => 'Fecha fuera de rango'],
        ]],
    ]]));
}

describe('contrato Finkok: timbrado', function () {
    it('éxito directo regresa checkProcess true con uuid y xml', function () {
        [$pac] = makeStampablePac();
        $fake = new FakeSoapCaller(stampSuccessResult());
        $pac->setSoapCaller($fake);

        $response = $pac->stampInvoice();

        expect($response->getCheckProcess())->toBeTrue()
            ->and($response->getUuid())->toBe(REAL_UUID)
            ->and($response->getXml())->not->toBe('')
            ->and($fake->operations())->toBe(['stamp']);
    });

    it('timbrado previamente (307) recupera el resultado original vía stamped', function () {
        [$pac] = makeStampablePac();
        $fake = new FakeSoapCaller(stampPreviouslyStampedResult(), stampedCompletedResult());
        $pac->setSoapCaller($fake);

        $response = $pac->stampInvoice();

        expect($response->getCheckProcess())->toBeTrue()
            ->and($response->getUuid())->toBe(REAL_UUID)
            ->and($response->getXml())->toContain('TFD')
            ->and($fake->operations())->toBe(['stamp', 'stamped']);
    });

    it('timbrado asíncrono en cola se resuelve consultando stamped', function () {
        [$pac] = makeStampablePac();
        // stamp responde "recibido" sin xml -> stamped pendiente -> stamped completo.
        $stampReceived = json_decode(json_encode(['stampResult' => [
            'UUID' => REAL_UUID,
            'CodEstatus' => 'Comprobante recibido satisfactoriamente',
            'Incidencias' => new stdClass,
        ]]));
        $fake = new FakeSoapCaller($stampReceived, stampedPendingResult(), stampedCompletedResult());
        $pac->setSoapCaller($fake);

        $response = $pac->stampInvoice();

        expect($response->getCheckProcess())->toBeTrue()
            ->and($response->getXml())->not->toBe('')
            ->and($fake->operations())->toBe(['stamp', 'stamped', 'stamped']);
    });

    it('timbrado asíncrono que nunca termina lanza PacStampInProgressException', function () {
        [$pac] = makeStampablePac();
        $stampReceived = json_decode(json_encode(['stampResult' => [
            'CodEstatus' => 'Comprobante recibido satisfactoriamente',
            'Incidencias' => new stdClass,
        ]]));
        $fake = new FakeSoapCaller(
            $stampReceived,
            stampedPendingResult(),
            stampedPendingResult(),
            stampedPendingResult(),
            stampedPendingResult(),
            stampedPendingResult(),
        );
        $pac->setSoapCaller($fake);

        expect(fn () => $pac->stampInvoice())->toThrow(PacStampInProgressException::class);
    });

    it('incidencia única regresa rejected y la persiste en invoice_incidents', function () {
        [$pac, $invoice] = makeStampablePac();
        $pac->setSoapCaller(new FakeSoapCaller(stampWithSingleIncidenceResult()));

        $response = $pac->stampInvoice();

        expect($response->getCheckProcess())->toBeFalse()
            ->and($response->getIncidenciaCodigoError())->toBe('301')
            ->and($response->getIncidenciaMensaje())->toContain('XML mal formado')
            ->and($response->getIncidenciaMensaje())->toContain('Elemento Concepto inválido')
            ->and(InvoiceIncident::where('invoice_id', $invoice->id)->count())->toBe(1);
    });

    it('múltiples incidencias se persisten todas', function () {
        [$pac, $invoice] = makeStampablePac();
        $pac->setSoapCaller(new FakeSoapCaller(stampWithMultipleIncidencesResult()));

        $response = $pac->stampInvoice();

        expect($response->getCheckProcess())->toBeFalse()
            ->and($response->getIncidenciaMensaje())->toContain('1 incidencia(s) más')
            ->and(InvoiceIncident::where('invoice_id', $invoice->id)->count())->toBe(2);
    });

    it('factura ya timbrada lanza excepción sin cachear la relación en la instancia', function () {
        [$pac, $invoice] = makeStampablePac();
        $cfdi = new InvoiceCfdi;
        $cfdi->invoice_id = $invoice->id;
        $cfdi->uuid = REAL_UUID;
        $cfdi->save();

        expect(fn () => $pac->stampInvoice())->toThrow(InvoiceAlreadyStampedException::class)
            ->and($invoice->relationLoaded('invoiceCfdi'))->toBeFalse();
    });

    it('borrador fuera de la ventana de 72 horas lanza StaleCfdiDraftException', function () {
        $fechaVieja = (new DateTimeImmutable('-4 days', new DateTimeZone('America/Mexico_City')))->format('Y-m-d\TH:i:s');
        [$pac] = makeStampablePac(freshDraftXml($fechaVieja));
        $pac->setSoapCaller(new FakeSoapCaller);

        expect(fn () => $pac->stampInvoice())->toThrow(StaleCfdiDraftException::class);
    });

    it('borrador registrado pero con archivo inexistente lanza InvoiceDocumentMissingException', function () {
        [$pac, $invoice] = makeStampablePac();
        Storage::disk('local')->delete('drafts/invoice-'.$invoice->id.'-draft.xml');
        $pac->setSoapCaller(new FakeSoapCaller);

        expect(fn () => $pac->stampInvoice())->toThrow(InvoiceDocumentMissingException::class);
    });
});

describe('contrato Finkok: recuperación de XML', function () {
    it('get_xml encuentra el UUID y regresa el xml', function () {
        [$pac, $invoice] = makeStampablePac();
        $cfdi = new InvoiceCfdi;
        $cfdi->invoice_id = $invoice->id;
        $cfdi->uuid = REAL_UUID;
        $cfdi->save();

        $getXmlFound = json_decode(json_encode(['get_xmlResult' => ['xml' => '<cfdi:Comprobante>rescatado</cfdi:Comprobante>']]));
        $fake = new FakeSoapCaller($getXmlFound);
        $pac->setSoapCaller($fake);

        $response = $pac->getXmlStamped();

        expect($response->getCheckProcess())->toBeTrue()
            ->and($fake->operations())->toBe(['get_xml']);
    });

    it('get_xml sin resultado cae al fallback stamped con el borrador', function () {
        [$pac, $invoice] = makeStampablePac();
        $cfdi = new InvoiceCfdi;
        $cfdi->invoice_id = $invoice->id;
        $cfdi->uuid = REAL_UUID;
        $cfdi->save();

        // Grabada del demo Finkok: get_xml responde error "UUID Does not Exists".
        $getXmlNotFound = json_decode(json_encode(['get_xmlResult' => ['error' => 'UUID Does not Exists']]));
        $fake = new FakeSoapCaller($getXmlNotFound, stampedCompletedResult());
        $pac->setSoapCaller($fake);

        $response = $pac->getXmlStamped();

        expect($response->getCheckProcess())->toBeTrue()
            ->and($response->getXml())->toContain('TFD')
            ->and($fake->operations())->toBe(['get_xml', 'stamped']);
    });
});

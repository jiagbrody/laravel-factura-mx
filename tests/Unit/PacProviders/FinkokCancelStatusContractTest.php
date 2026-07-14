<?php

declare(strict_types=1);

use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Exceptions\InvoiceNotStampedException;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCfdi;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Models\InvoiceIncident;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok\FinkokPac;
use JiagBrody\LaravelFacturaMx\Tests\Support\FakeSoapCaller;

/*
 * Tests de contrato contra respuestas REALES de Finkok (grabadas de los logs
 * SOAP, junio-julio 2026) para los flujos de cancelación (sign_cancel) y
 * consulta de estatus ante el SAT (get_sat_status).
 */

const CANCEL_UUID = '8BC5C440-B346-5FFC-BD36-A49D0A9944E9';

function makeCancellablePac(bool $withCfdi = true): FinkokPac
{
    $company = InvoiceCompany::factory()->create();
    $invoice = Invoice::factory()->create(['invoice_company_id' => $company->id]);

    if ($withCfdi) {
        $cfdi = new InvoiceCfdi;
        $cfdi->invoice_id = $invoice->id;
        $cfdi->uuid = CANCEL_UUID;
        $cfdi->save();
    }

    $pac = new FinkokPac($invoice);
    $pac->setInvoiceCompanyHelper($company);

    return $pac;
}

/*
 * Grabada del demo Finkok: petición aceptada (201) con acuse del SAT.
 */
function signCancelAcceptedResult(): stdClass
{
    return json_decode(json_encode(['sign_cancelResult' => [
        'Folios' => ['Folio' => [
            'UUID' => CANCEL_UUID,
            'EstatusUUID' => '201',
            'EstatusCancelacion' => 'Petición de cancelación realizada exitosamente',
        ]],
        'Acuse' => '<s:Envelope><s:Body><CancelaCFDResponse><CancelaCFDResult Fecha="2026-06-27T00:14:54" RfcEmisor="IIA040805DZ4"><Folios><UUID>'.CANCEL_UUID.'</UUID><EstatusUUID>201</EstatusUUID></Folios></CancelaCFDResult></CancelaCFDResponse></s:Body></s:Envelope>',
    ]]));
}

function signCancelPreviouslyCancelledResult(): stdClass
{
    return json_decode(json_encode(['sign_cancelResult' => [
        'Folios' => ['Folio' => [
            'UUID' => CANCEL_UUID,
            'EstatusUUID' => '202',
            'EstatusCancelacion' => 'UUID Previamente cancelado',
        ]],
    ]]));
}

function signCancelRejectedResult(): stdClass
{
    return json_decode(json_encode(['sign_cancelResult' => [
        'Folios' => ['Folio' => [
            'UUID' => CANCEL_UUID,
            'EstatusUUID' => '205',
            'EstatusCancelacion' => 'UUID No Existe',
        ]],
        'CodEstatus' => '',
    ]]));
}

/*
 * Grabada del demo Finkok: nótese que cuando el CFDI está vigente NO viene el
 * nodo EstatusCancelacion, y que el emisor de prueba dispara validación EFOS.
 */
function satStatusResult(string $estado, ?string $estatusCancelacion = null): stdClass
{
    $sat = [
        'DetallesValidacionEFOS' => 'RFC Emisor del CFDI se encuentra dentro de la lista de Empresa que Factura Operaciones Simuladas (EFOS).',
        'EsCancelable' => 'Cancelable sin aceptación',
        'ValidacionEFOS' => '100',
        'CodigoEstatus' => 'S - Comprobante obtenido satisfactoriamente.',
        'Estado' => $estado,
    ];

    if ($estatusCancelacion !== null) {
        $sat['EstatusCancelacion'] = $estatusCancelacion;
    }

    return json_decode(json_encode(['get_sat_statusResult' => ['sat' => $sat]]));
}

describe('contrato Finkok: cancelación (sign_cancel)', function () {
    it('201 aceptada regresa checkProcess true con el acuse', function () {
        $pac = makeCancellablePac();
        $fake = new FakeSoapCaller(signCancelAcceptedResult());
        $pac->setSoapCaller($fake);

        $response = $pac->cancelInvoice(InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED);

        expect($response->checkProcess)->toBeTrue()
            ->and($response->estatusUUID)->toBe('201')
            ->and($response->hasAcuse())->toBeTrue()
            ->and($response->acuse)->toContain('CancelaCFDResponse')
            ->and($fake->operations())->toBe(['sign_cancel'])
            ->and($fake->calls[0]['params']['UUIDS']['UUID']['Motivo'])->toBe('03');
    });

    it('202 previamente cancelado es éxito idempotente sin acuse', function () {
        $pac = makeCancellablePac();
        $pac->setSoapCaller(new FakeSoapCaller(signCancelPreviouslyCancelledResult()));

        $response = $pac->cancelInvoice(InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED);

        expect($response->checkProcess)->toBeTrue()
            ->and($response->estatusUUID)->toBe('202')
            ->and($response->hasAcuse())->toBeFalse();
    });

    it('rechazo (205) regresa checkProcess false y persiste el incidente', function () {
        $pac = makeCancellablePac();
        $pac->setSoapCaller(new FakeSoapCaller(signCancelRejectedResult()));

        $response = $pac->cancelInvoice(InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED);

        expect($response->checkProcess)->toBeFalse()
            ->and($response->estatusUUID)->toBe('205')
            ->and($response->estatusCancelacion)->toBe('UUID No Existe')
            ->and(InvoiceIncident::where('code', '205')->count())->toBe(1);
    });

    it('motivo 01 con relación envía el FolioSustitucion', function () {
        $pac = makeCancellablePac();
        $fake = new FakeSoapCaller(signCancelAcceptedResult());
        $pac->setSoapCaller($fake);

        $replacementUuid = '9D525202-7069-5A3A-94CA-01BF2CD8EDA9';
        $pac->cancelInvoice(InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED, $replacementUuid);

        $uuidsParam = $fake->calls[0]['params']['UUIDS']['UUID'];

        expect($uuidsParam['Motivo'])->toBe('01')
            ->and($uuidsParam['FolioSustitucion'])->toBe($replacementUuid);
    });

    it('factura sin CFDI lanza InvoiceNotStampedException', function () {
        $pac = makeCancellablePac(withCfdi: false);
        $pac->setSoapCaller(new FakeSoapCaller);

        expect(fn () => $pac->cancelInvoice(InvoiceCfdiCancelTypeEnum::NEW_NOT_EXECUTED))
            ->toThrow(InvoiceNotStampedException::class);
    });
});

describe('contrato Finkok: estatus ante el SAT (get_sat_status)', function () {
    function makeStatusReadyPac(): FinkokPac
    {
        $pac = makeCancellablePac();
        $pac->setReceptorRfc('XAXX010101000');
        $pac->setTotal('612.90');

        return $pac;
    }

    it('Vigente mapea al enum VIGENT y conserva los datos EFOS', function () {
        $pac = makeStatusReadyPac();
        $fake = new FakeSoapCaller(satStatusResult('Vigente'));
        $pac->setSoapCaller($fake);

        $response = $pac->statusInvoice();

        expect($response->checkProcess)->toBeTrue()
            ->and($response->estado)->toBe('Vigente')
            ->and($response->invoiceStatusEnum)->toBe(InvoiceStatusEnum::VIGENT)
            ->and($response->esCancelable)->toBe('Cancelable sin aceptación')
            ->and($response->validacionEFOS)->toBe('100')
            ->and($response->estatusCancelacion)->toBe('')
            ->and($fake->operations())->toBe(['get_sat_status']);
    });

    it('Cancelado mapea al enum CANCELED', function () {
        $pac = makeStatusReadyPac();
        $pac->setSoapCaller(new FakeSoapCaller(satStatusResult('Cancelado', 'Cancelado sin aceptación')));

        $response = $pac->statusInvoice();

        expect($response->invoiceStatusEnum)->toBe(InvoiceStatusEnum::CANCELED)
            ->and($response->estatusCancelacion)->toBe('Cancelado sin aceptación');
    });

    it('estado desconocido del SAT regresa enum null, nunca borrador', function () {
        $pac = makeStatusReadyPac();
        $pac->setSoapCaller(new FakeSoapCaller(satStatusResult('No Encontrado')));

        $response = $pac->statusInvoice();

        expect($response->checkProcess)->toBeTrue()
            ->and($response->estado)->toBe('No Encontrado')
            ->and($response->invoiceStatusEnum)->toBeNull();
    });

    it('factura sin CFDI lanza InvoiceNotStampedException', function () {
        $pac = makeCancellablePac(withCfdi: false);
        $pac->setReceptorRfc('XAXX010101000');
        $pac->setTotal('612.90');
        $pac->setSoapCaller(new FakeSoapCaller);

        expect(fn () => $pac->statusInvoice())->toThrow(InvoiceNotStampedException::class);
    });
});

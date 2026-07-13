<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use JiagBrody\LaravelFacturaMx\Exceptions\InvoiceAlreadyStampedException;
use JiagBrody\LaravelFacturaMx\Exceptions\InvoiceDocumentMissingException;
use JiagBrody\LaravelFacturaMx\Exceptions\PacUnexpectedResponseException;
use JiagBrody\LaravelFacturaMx\Exceptions\StaleCfdiDraftException;
use JiagBrody\LaravelFacturaMx\Models\InvoiceIncident;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;
use JiagBrody\LaravelFacturaMx\Services\Document\Helpers as DocumentHelpers;
use stdClass;

trait StampTrait
{
    private function stamp(): PacStampResponse
    {
        $this->detectLogicErrorInStamp();

        $draftXmlContent = $this->obtainDraftXmlContent();
        $this->assertDraftIsNotStale($draftXmlContent);

        $params = [
            'xml' => $draftXmlContent,
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
        ];

        $response = $this->soapCaller()->call($this->stampUrlFinkok, 'stamp', $params, 'cfdi_finkok_stamp');

        if (! isset($response->stampResult)) {
            throw PacUnexpectedResponseException::missingNode('stamp', 'stampResult');
        }

        return $this->buildStampResponse($response->stampResult);
    }

    private function obtainDraftXmlContent(): string
    {
        $xmlFile = $this->invoice->xmlInvoiceDocument;

        if ($xmlFile === null) {
            throw new InvoiceDocumentMissingException('La factura no tiene XML de borrador registrado; genera el CFDI antes de enviarlo a timbrar.');
        }

        $content = DocumentHelpers::obtainExistingDocumentFile($xmlFile);

        if ($content === null) {
            throw new InvoiceDocumentMissingException('El XML de borrador está registrado pero el archivo no existe en el disco "'.$xmlFile->storage.'".');
        }

        return $content;
    }

    private function buildStampResponse(stdClass $result): PacStampResponse
    {
        $codEstatus = (string) ($result->CodEstatus ?? '');

        // TIMBRADO ("timbrado previamente" regresa el mismo CFDI: es idempotente).
        if (in_array($codEstatus, ['Comprobante timbrado satisfactoriamente', 'Comprobante timbrado previamente'], true)) {
            $uuid = (string) ($result->UUID ?? '');
            $xml = (string) ($result->xml ?? '');

            // "Timbrado previamente" puede llegar SIN el XML; se recupera del
            // PAC (get_xml) para no reportar un timbrado exitoso sin archivo.
            if ($xml === '' && $uuid !== '') {
                try {
                    $recovery = $this->recoverStampedXmlByUuid($uuid);
                    $xml = $recovery->getCheckProcess() ? $recovery->getXml() : '';
                } catch (\Throwable) {
                    // El intercambio queda en el log SOAP; quien persista
                    // decidirá cómo fallar con un mensaje claro.
                }
            }

            return PacStampResponse::stamped(uuid: $uuid, codEstatus: $codEstatus, xml: $xml);
        }

        // INCIDENCIAS: Finkok devuelve un objeto cuando hay una sola, un array
        // cuando hay varias, y puede omitir el nodo por completo.
        $incidencias = $result->Incidencias->Incidencia ?? null;
        $incidencias = is_array($incidencias) ? array_values($incidencias) : array_filter([$incidencias]);

        if ($incidencias === []) {
            return PacStampResponse::rejected(
                codEstatus: $codEstatus,
                incidenciaIdIncidencia: '',
                incidenciaCodigoError: '',
                incidenciaMensaje: $codEstatus !== '' ? $codEstatus : 'El PAC rechazó el timbrado sin reportar incidencias.',
                uuid: (string) ($result->UUID ?? ''),
            );
        }

        foreach ($incidencias as $incidencia) {
            $this->saveIncident($incidencia);
        }

        $primera = $incidencias[0];
        $message = (string) ($primera->MensajeIncidencia ?? '');
        if (isset($primera->ExtraInfo) && $primera->ExtraInfo) {
            $message .= ' - '.$primera->ExtraInfo;
        }
        if (count($incidencias) > 1) {
            $message .= ' (y '.(count($incidencias) - 1).' incidencia(s) más registradas en invoice_incidents)';
        }

        return PacStampResponse::rejected(
            codEstatus: $codEstatus,
            incidenciaIdIncidencia: (string) ($primera->IdIncidencia ?? ''),
            incidenciaCodigoError: (string) ($primera->CodigoError ?? ''),
            incidenciaMensaje: $message,
            uuid: (string) ($result->UUID ?? ''),
        );
    }

    private function detectLogicErrorInStamp(): void
    {
        if ($this->invoice->invoiceCfdi) {
            throw InvoiceAlreadyStampedException::withUuid((string) $this->invoice->invoiceCfdi->uuid);
        }
    }

    /**
     * El SAT rechaza CFDI cuya Fecha sea más antigua que 72 horas. Detectarlo
     * antes de enviar evita quemar el intento; configurable (y desactivable
     * con 0) vía "stamp_draft_max_age_hours".
     */
    private function assertDraftIsNotStale(string $xmlContent): void
    {
        $maxAgeHours = (int) config('jiagbrody-laravel-factura-mx.stamp_draft_max_age_hours', 71);
        if ($maxAgeHours <= 0) {
            return;
        }

        $document = new \DOMDocument;
        if (@$document->loadXML($xmlContent) === false || $document->documentElement === null) {
            return; // XML ilegible: que el PAC reporte el problema real.
        }

        $fecha = $document->documentElement->getAttribute('Fecha');
        if ($fecha === '') {
            return;
        }

        $timezone = new \DateTimeZone((string) config('jiagbrody-laravel-factura-mx.default_timezone', 'America/Mexico_City'));
        $fechaCfdi = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $fecha, $timezone);
        if ($fechaCfdi === false) {
            return;
        }

        $ageInHours = ((new \DateTimeImmutable('now', $timezone))->getTimestamp() - $fechaCfdi->getTimestamp()) / 3600;

        if ($ageInHours > $maxAgeHours) {
            throw StaleCfdiDraftException::forFecha($fecha, $maxAgeHours);
        }
    }

    /*
     * SAVE INCIDENT DIRECTLY WITH THE RESPONSE FROM THE STAMPING PROVIDER
     */
    private function saveIncident($incident): void
    {
        $invoiceIncident = new InvoiceIncident;
        $invoiceIncident->user_id = auth()->id();
        $invoiceIncident->invoice_id = $this->invoice->id;
        $invoiceIncident->supplier = 'Finkok';
        $invoiceIncident->code = (string) ($incident->CodigoError ?? '');
        $invoiceIncident->message = (string) ($incident->MensajeIncidencia ?? '');
        $invoiceIncident->additional_details = json_encode($incident);
        $invoiceIncident->save();
    }
}

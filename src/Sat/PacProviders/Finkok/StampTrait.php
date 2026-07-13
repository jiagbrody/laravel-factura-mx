<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use JiagBrody\LaravelFacturaMx\Models\InvoiceIncident;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;
use JiagBrody\LaravelFacturaMx\Services\Document\DocumentService;
use JiagBrody\LaravelFacturaMx\Services\SaveSoapRequestResponseLogService;
use SoapClient;

trait StampTrait
{
    /**
     * @throws \Exception
     */
    private function stamp(): PacStampResponse
    {
        $this->detectLogicErrorInStamp();

        $xmlFile = $this->invoice->xmlInvoiceDocument;

        $documentService = new DocumentService;
        $draftXmlDocument = $documentService->helpers::obtainExistingDocumentFile($xmlFile);

        $params = [
            'xml' => $draftXmlDocument,
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
        ];

        try {
            $client = new SoapClient($this->stampUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall('stamp', [$params]);

            if (! isset($response->stampResult)) {
                throw new \Exception('El pac no devuelve: "stampResult"');
            }

            (new SaveSoapRequestResponseLogService)->make($client, 'Finkok:stamp', 'cfdi_finkok_stamp');

            return $this->setStampResponse($response);

        } catch (\SoapFault $e) {
            throw new \Exception('Fallo en el timbrado por un error del servicio SOAP al proveedor PAC. error: '.$e->getMessage().' error detallado: '.$e->getTraceAsString());
        }
    }

    private function setStampResponse($pacResponse): PacStampResponse
    {
        $result = $pacResponse->stampResult;

        $response = new PacStampResponse;
        $response->setUuid($result->UUID ?? '');
        $response->setCodEstatus($result->CodEstatus ?? '');

        // TIMBRADO
        if (isset($result->CodEstatus) && (($result->CodEstatus === 'Comprobante timbrado satisfactoriamente') || ($result->CodEstatus === 'Comprobante timbrado previamente'))) {
            $response->setCheckProcess(true);
            $response->setXml($result->xml);

            return $response;
        }

        // EXISTING INCIDENCE
        // El WS de Finkok devuelve un objeto cuando hay una sola incidencia y
        // un array cuando hay varias; también puede no incluir el nodo.
        $incidencias = $result->Incidencias->Incidencia ?? null;
        $incidencias = is_array($incidencias) ? array_values($incidencias) : array_filter([$incidencias]);

        $response->setCheckProcess(false);

        if ($incidencias === []) {
            $response->setIncidenciaIdIncidencia('');
            $response->setIncidenciaCodigoError('');
            $response->setIncidenciaMensaje((string) ($result->CodEstatus ?? 'El PAC rechazó el timbrado sin reportar incidencias.'));

            return $response;
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

        $response->setIncidenciaIdIncidencia((string) ($primera->IdIncidencia ?? ''));
        $response->setIncidenciaMensaje($message);
        $response->setIncidenciaCodigoError((string) ($primera->CodigoError ?? ''));

        return $response;
    }

    private function detectLogicErrorInStamp(): void
    {
        // OJO: la relación se llama "invoiceCfdi"; con "$this->invoice->cfdi"
        // Eloquent devolvía null siempre y este guard nunca se activaba,
        // permitiendo timbrar dos veces la misma factura.
        if ($this->invoice->invoiceCfdi) {
            throw new \Exception('Esta factura ya se encuentra timbrada (UUID: '.$this->invoice->invoiceCfdi->uuid.').');
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

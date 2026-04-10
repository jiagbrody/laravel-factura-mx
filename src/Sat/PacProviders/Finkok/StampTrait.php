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

            if (!isset($response->stampResult)) {
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
        $incidencia = $result->Incidencias->Incidencia;
        $this->saveIncident($incidencia);

        $response->setCheckProcess(false);
        $response->setIncidenciaIdIncidencia($incidencia->IdIncidencia);
        $message = $incidencia->MensajeIncidencia;
        if ($incidencia->MensajeIncidencia) {
            $message .= ' - '.$incidencia->ExtraInfo;
        }
        $response->setIncidenciaMensaje($message);
        $response->setIncidenciaCodigoError($incidencia->CodigoError);

        return $response;
    }

    private function detectLogicErrorInStamp(): void
    {
        if ($this->invoice->cfdi) {
            throw new \Exception('Esta factura ya se encuentra timbrada.');
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
        $invoiceIncident->code = $incident->CodigoError;
        $invoiceIncident->message = $incident->MensajeIncidencia;
        $invoiceIncident->additional_details = json_encode($incident);
        $invoiceIncident->save();
    }
}

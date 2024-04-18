<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use Exception;
use JiagBrody\LaravelFacturaMx\Models\InvoiceDocument;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStampResponse;
use JiagBrody\LaravelFacturaMx\Services\SaveSoapRequestResponseLogService;
use SoapClient;

trait StampTrait
{
    private function stamp(): PacStampResponse
    {
        $this->detectLogicErrorInStamp();

        $xmlFile = $this->invoice->xmlInvoiceDocument;
        $draftXmlDocument = InvoiceDocument::obtainDocumentContent($xmlFile);

        $params = [
            'xml' => $draftXmlDocument,
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
        ];

        try {
            $client = new SoapClient($this->stampUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall('quick_stamp', [$params]);

            (new SaveSoapRequestResponseLogService)->make($client, 'Finkok:quick_stamp', 'cfdi_finkok_quick_stamp');

            return $this->getStampResponse($response);

        } catch (exception $e) {
            abort(422, $e->getMessage());
        }
    }

    private function getStampResponse($pacResponse): PacStampResponse
    {
        $result = $pacResponse->quick_stampResult;
        $response = new PacStampResponse;
        $response->setUuid($result->UUID ?? '');
        $response->setCodEstatus($result->CodEstatus ?? '');

        //TIMBRADO
        if (isset($result->CodEstatus) && ($result->CodEstatus === 'Comprobante timbrado satisfactoriamente')) {
            $response->setCheckProcess(true);
            $response->setXml($result->xml);

            return $response;
        }

        //NO TRIMBRADO
        $response->setCheckProcess(false);
        $incidencia = $result->Incidencias->Incidencia;
        $response->setIncidenciaIdIncidencia($incidencia->IdIncidencia);
        $message = $incidencia->MensajeIncidencia;
        if ($incidencia->MensajeIncidencia) {
            $message .= ' - ' . $incidencia->ExtraInfo;
        }
        $response->setIncidenciaMensaje($message);
        $response->setIncidenciaCodigoError($incidencia->CodigoError);

        return $response;
    }

    private function detectLogicErrorInStamp(): void
    {
        if ($this->invoice->cfdi) {
            abort(403, 'Esta factura ya se encuentra timbrada!');
        }
    }
}

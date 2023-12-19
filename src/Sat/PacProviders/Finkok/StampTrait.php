<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use App\Services\Logs\SaveSoapRequestResponseLogService;
use App\Services\PAC\Providers\PacStampResponse;
use Exception;
use SoapClient;

trait StampTrait
{
    private function stamp(): PacStampResponse
    {
        $this->detectLogicErrorInStamp();

        $xmlFile = $this->invoice->xmlInvoiceDocument;
        $draftCfdi = Document::obtainDocumentContent($xmlFile);

        $params = [
            'xml' => $draftCfdi,
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
        ];

        try {
            $client = new SoapClient($this->stampUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall('quick_stamp', [$params]);

            (new SaveSoapRequestResponseLogService)->make($client, 'Finkok:quick_stamp', 'cfdi_finkok_quick_stamp');

            return $this->getPacStampResponse($response);
        } catch (exception $e) {
            abort(422, $e->getMessage());
        }
    }

    private function getPacStampResponse($responsePac): PacStampResponse
    {
        $result = $responsePac->quick_stampResult;
        $res = new PacStampResponse;
        $res->uuid = $result->UUID ?? '';
        $res->codEstatus = $result->CodEstatus ?? '';

        if (isset($result->CodEstatus) && ($result->CodEstatus === 'Comprobante timbrado satisfactoriamente')) {
            $res->checkProcess = true;
            $res->xml = $result->xml;

            return $res;
        }

        $incidencia = $result->Incidencias->Incidencia;
        $res->incidenciaIdIncidencia = $incidencia->IdIncidencia;
        $res->incidenciaMensaje = $incidencia->MensajeIncidencia;
        if ($incidencia->MensajeIncidencia) {
            $res->incidenciaMensaje .= ' - '.$incidencia->ExtraInfo;
        }
        $res->incidenciaCodigoError = $incidencia->CodigoError;

        return $res;
        //abort(403, $incidencia->MensajeIncidencia.' - '.$incidencia->ExtraInfo);
    }

    private function detectLogicErrorInStamp(): void
    {
        if ($this->invoice->cfdi) {
            abort(403, 'Esta factura ya se encuentra timbrada!');
        }
    }
}

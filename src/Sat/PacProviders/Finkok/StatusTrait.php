<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use Exception;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceStatusEnum;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStatusResponse;
use JiagBrody\LaravelFacturaMx\Services\SaveSoapRequestResponseLogService;
use SoapClient;

trait StatusTrait
{
    protected PacStatusResponse $pacStatusResponse;

    private function getStatusCfdiSat(): PacStatusResponse
    {
        if ($this->invoice->invoiceCfdi === null) {
            abort(403, 'Timbre no detectado.');
        }

        if (! $this->invoice->invoiceCfdi->xmlInvoiceDocument) {
            abort(403, 'Cfdi timbrado pero no están generados los documentos. Es necesario generarlos.');
        }

        $params = [
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
            'taxpayer_id' => $this->invoiceCompanyHelper->rfc,
            'rtaxpayer_id' => $this->invoice->invoiceDetail->receptor_rfc,
            'uuid' => $this->invoice->invoiceCfdi->uuid,
            'total' => $this->invoice->invoiceDetail->total,
        ];

        try {
            $client = new SoapClient($this->statusUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall('get_sat_status', [$params]);

            (new SaveSoapRequestResponseLogService)->make($client, 'Finkok:get_sat_status', 'cfdi_finkok_get_sat_status');
        } catch (exception $e) {
            abort(422, $e->getMessage());
        }

        $sat = $response->get_sat_statusResult->sat;
        $estatusCancelacion = (isset($sat->EstatusCancelacion)) ? $sat->EstatusCancelacion : '';

        $response = new PacStatusResponse();
        $response->setCheckProcess(true);

        $response->setEstado($sat->Estado);
        if ($response->estado === 'Cancelado') {
            $response->setInvoiceStatusEnum(InvoiceStatusEnum::CANCELED);
        } elseif ($response->estado === 'Vigente') {
            $response->setInvoiceStatusEnum(InvoiceStatusEnum::VIGENT);
        }

        $response->setDetallesValidacionEFOS($sat->DetallesValidacionEFOS);
        $response->setValidacionEFOS($sat->ValidacionEFOS);
        $response->setEsCancelable($sat->EsCancelable);
        $response->setCodigoEstatus($sat->CodigoEstatus);
        $response->setEstatusCancelacion($estatusCancelacion);

        return $response;
    }
}

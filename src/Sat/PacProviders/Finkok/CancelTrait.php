<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use Exception;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Models\InvoiceIncident;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Services\SaveSoapRequestResponseLogService;
use SoapClient;

trait CancelTrait
{
    private InvoiceCfdiCancelTypeEnum $cfdiCancelTypeEnum;

    private ?string $replace_uuid;

    protected PacCancelResponse $cancelResponse;

    private function cancel(InvoiceCfdiCancelTypeEnum $cancelType, ?string $replacementUUID = ''): PacCancelResponse
    {
        $this->cfdiCancelTypeEnum = $cancelType;
        $this->replace_uuid = $replacementUUID;
        $this->cancelResponse = new PacCancelResponse;

        $this->processSignCancel();

        // Verificación de cancelación
        // $this->get_receipt();

        return $this->cancelResponse;

        // UPDATE 2022-09-27: SE VA A DEJAR DE USAR ESTE METODO YA QUE VEO COMPLICADO MANTENER ALGO QUE EN EL DEMO DEL SAT NO VA A FUNCIONAR.
        // AUNQUE EN PRODUCCION LO IDEAL ES USAR "cancelSignature".
    }

    public function get_receipt(): void
    {
        $params = [
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
            'taxpayer_id' => $this->invoiceCompanyHelper->rfc,
            'uuid' => $this->invoice->invoiceCfdi->uuid,
            'type' => 'C',
        ];

        try {
            $client = new SoapClient($this->cancelUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall('get_receipt', [$params]);
            // dd($client->__getLastRequest(), $client->__getLastResponse());
            // dd($response);
        } catch (exception $e) {
            abort(422, $e->getMessage());
        }
    }

    private function processSignCancel(): void
    {
        $uuidsCollect = collect(['UUID' => $this->invoice->invoiceCfdi->uuid]);
        $uuidsCollect->put('Motivo', $this->cfdiCancelTypeEnum->getSatId());
        if ($this->cfdiCancelTypeEnum === InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED) {
            $uuidsCollect->put('FolioSustitucion', $this->replace_uuid);
        }
        $addons = ['UUID' => $uuidsCollect->toArray()];

        $params = [
            'UUIDS' => $addons,
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
            'taxpayer_id' => $this->invoiceCompanyHelper->rfc,
            'serial' => $this->invoiceCompanyHelper->serialNumber,
        ];

        try {
            $client = new SoapClient($this->cancelUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall('sign_cancel', [$params]);
            // dd($client->__getLastRequest(), $client->__getLastResponse());

            (new SaveSoapRequestResponseLogService)->make($client, 'Finkok:sign_cancel', 'cfdi_finkok_sign_cancel');

            $this->setResponsePac($response->sign_cancelResult);
        } catch (exception $e) {
            abort(422, $e->getMessage());
        }
    }

    private function setResponsePac($cancelResult): void
    {
        $folio = $cancelResult->Folios->Folio;

        if (! isset($folio)) {
            abort(422, $cancelResult->CodEstatus);
        }

        $this->cancelResponse->setUUID($folio->UUID);
        $this->cancelResponse->setEstatusUUID($folio->EstatusUUID);

        if ($folio->EstatusUUID === '201') {
            $this->cancelResponse->setCheckProcess(true);
            $this->cancelResponse->setAcuse($cancelResult->Acuse);
            $this->cancelResponse->setEstatusCancelacion('Petición de cancelación realizada exitosamente');

            return;
        }

        if ($folio->EstatusUUID === '202') {
            $this->cancelResponse->setCheckProcess(true);
            $this->cancelResponse->setEstatusCancelacion('UUID previamente cancelado');

            return;
        }

        $this->cancelResponse->setEstatusCancelacion($folio->EstatusCancelacion);
        $this->cancelResponse->setCheckProcess(false);
        $this->saveErrorCancel($this->cancelResponse, $folio);
    }

    private function saveErrorCancel(PacCancelResponse $response, $folio): void
    {
        $invoiceIncident = new InvoiceIncident;
        $invoiceIncident->user_id = auth()->id();
        $invoiceIncident->invoice_id = $this->invoice->id;
        $invoiceIncident->supplier = 'Finkok';
        $invoiceIncident->code = $this->cancelResponse->estatusCancelacion;
        $invoiceIncident->message = $this->cancelResponse->estatusUUID;
        $invoiceIncident->additional_details = json_encode($folio);
        $invoiceIncident->save();
    }
}

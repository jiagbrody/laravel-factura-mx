<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Exceptions\InvoiceNotStampedException;
use JiagBrody\LaravelFacturaMx\Exceptions\PacUnexpectedResponseException;
use JiagBrody\LaravelFacturaMx\Models\InvoiceIncident;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacCancelResponse;
use stdClass;

trait CancelTrait
{
    private function cancel(InvoiceCfdiCancelTypeEnum $cancelType, ?string $replacementUUID = null): PacCancelResponse
    {
        if ($this->invoice->invoiceCfdi === null) {
            throw InvoiceNotStampedException::forOperation('cancelar');
        }

        $uuidsCollect = collect(['UUID' => $this->invoice->invoiceCfdi->uuid]);
        $uuidsCollect->put('Motivo', $cancelType->getSatId());
        if ($cancelType === InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED) {
            $uuidsCollect->put('FolioSustitucion', $replacementUUID);
        }

        $params = [
            'UUIDS' => ['UUID' => $uuidsCollect->toArray()],
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
            'taxpayer_id' => $this->invoiceCompanyHelper->rfc,
            'serial' => $this->invoiceCompanyHelper->serialNumber,
        ];

        $response = $this->soapCaller()->call($this->cancelUrlFinkok, 'sign_cancel', $params, 'cfdi_finkok_sign_cancel');

        if (! isset($response->sign_cancelResult)) {
            throw PacUnexpectedResponseException::missingNode('sign_cancel', 'sign_cancelResult');
        }

        return $this->buildCancelResponse($response->sign_cancelResult);
    }

    private function buildCancelResponse(stdClass $cancelResult): PacCancelResponse
    {
        // Se envía un solo UUID, pero el nodo puede llegar como array.
        $folio = $cancelResult->Folios->Folio ?? null;
        if (is_array($folio)) {
            $folio = $folio[0] ?? null;
        }

        if ($folio === null) {
            throw PacUnexpectedResponseException::withStatus('sign_cancel', (string) ($cancelResult->CodEstatus ?? 'sin CodEstatus'));
        }

        $uuid = (string) ($folio->UUID ?? '');
        $estatusUUID = (string) ($folio->EstatusUUID ?? '');

        if ($estatusUUID === '201') {
            return PacCancelResponse::accepted(uuid: $uuid, acuse: (string) ($cancelResult->Acuse ?? ''));
        }

        if ($estatusUUID === '202') {
            return PacCancelResponse::previouslyCancelled(uuid: $uuid);
        }

        $response = PacCancelResponse::rejected(
            uuid: $uuid,
            estatusUUID: $estatusUUID,
            estatusCancelacion: (string) ($folio->EstatusCancelacion ?? ''),
        );

        $this->saveCancelIncident($response, $folio);

        return $response;
    }

    private function saveCancelIncident(PacCancelResponse $response, stdClass $folio): void
    {
        $invoiceIncident = new InvoiceIncident;
        $invoiceIncident->user_id = auth()->id();
        $invoiceIncident->invoice_id = $this->invoice->id;
        $invoiceIncident->supplier = 'Finkok';
        $invoiceIncident->code = $response->estatusUUID;
        $invoiceIncident->message = $response->estatusCancelacion;
        $invoiceIncident->additional_details = json_encode($folio);
        $invoiceIncident->save();
    }
}

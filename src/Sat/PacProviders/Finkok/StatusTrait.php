<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use JiagBrody\LaravelFacturaMx\Exceptions\InvoiceNotStampedException;
use JiagBrody\LaravelFacturaMx\Exceptions\PacUnexpectedResponseException;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacStatusResponse;

trait StatusTrait
{
    private function getStatusCfdiSat(): PacStatusResponse
    {
        if ($this->invoice->invoiceCfdi === null) {
            throw InvoiceNotStampedException::forOperation('consultar estatus ante el SAT');
        }

        $params = [
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
            'taxpayer_id' => $this->invoice->invoiceCompany->rfc,
            'rtaxpayer_id' => $this->receptorRfc,
            'uuid' => $this->invoice->invoiceCfdi->uuid,
            'total' => $this->total,
        ];

        $response = $this->soapCaller()->call($this->statusUrlFinkok, 'get_sat_status', $params, 'cfdi_finkok_get_sat_status');

        $sat = $response->get_sat_statusResult->sat ?? null;

        if ($sat === null) {
            throw PacUnexpectedResponseException::missingNode('get_sat_status', 'get_sat_statusResult.sat');
        }

        return PacStatusResponse::fromSat(
            estado: (string) ($sat->Estado ?? ''),
            esCancelable: (string) ($sat->EsCancelable ?? ''),
            codigoEstatus: (string) ($sat->CodigoEstatus ?? ''),
            estatusCancelacion: (string) ($sat->EstatusCancelacion ?? ''),
            validacionEFOS: (string) ($sat->ValidacionEFOS ?? ''),
            detallesValidacionEFOS: (string) ($sat->DetallesValidacionEFOS ?? ''),
        );
    }
}

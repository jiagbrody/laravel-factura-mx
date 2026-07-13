<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use JiagBrody\LaravelFacturaMx\Exceptions\InvoiceNotStampedException;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacRecoveryCfdiXmlResponse;

trait XmlStampedTrait
{
    private function getXmlStampedCfdiSat(): PacRecoveryCfdiXmlResponse
    {
        if ($this->invoice->invoiceCfdi === null) {
            throw InvoiceNotStampedException::forOperation('recuperar el XML timbrado');
        }

        $params = [
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
            'taxpayer_id' => $this->invoiceCompanyHelper->rfc,
            'uuid' => $this->invoice->invoiceCfdi->uuid,
            'invoice_type' => 'I', // Finkok solo acepta 'I' (CFDI) o 'R' (retenciones). https://wiki.finkok.com/en/home/webservices/utilerias/get_xml
        ];

        $data = $this->soapCaller()->call($this->utilitiesUrlFinkok, 'get_xml', $params, 'cfdi_finkok_get_xml');

        $xml = (string) ($data->get_xmlResult->xml ?? '');

        return $xml !== ''
            ? PacRecoveryCfdiXmlResponse::found($xml)
            : PacRecoveryCfdiXmlResponse::notFound();
    }
}

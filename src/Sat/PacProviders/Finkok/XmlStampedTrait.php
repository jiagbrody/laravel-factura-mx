<?php

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use Exception;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacRecoveryCfdiXmlResponse;
use JiagBrody\LaravelFacturaMx\Services\SaveSoapRequestResponseLogService;
use SoapClient;

trait XmlStampedTrait
{
    private function getXmlStampedCfdiSat()
    {
        $params = [
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
            'taxpayer_id' => $this->invoiceCompanyHelper->rfc,
            'uuid' => $this->invoice->invoiceCfdi->uuid,
            'invoice_type' => 'I', // En la documentacion de FINKOK solo se indica que se puede recibir 'I' o 'R' (El tipo de comprobante CFDI= 'I' Retenciones='R'.). Fuente: https://wiki.finkok.com/en/home/webservices/utilerias/get_xml
        ];

        try {
            $client = new SoapClient($this->utilitiesUrlFinkok, ['trace' => 1]);
            $data = $client->__soapCall('get_xml', [$params]);

            $response = new PacRecoveryCfdiXmlResponse;
            if ($data?->get_xmlResult?->xml) {
                $response->setCheckProcess(true);
                $response->setXml($data->get_xmlResult->xml);
            } else {
                $response->setCheckProcess(false);
                $response->setXml('');
            }

            (new SaveSoapRequestResponseLogService)->make($client, 'Finkok:get_xml', 'cfdi_finkok_get_xml');

            return $response;
        } catch (exception $e) {
            abort(422, $e->getMessage());
        }
    }
}

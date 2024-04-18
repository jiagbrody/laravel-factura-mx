<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use Exception;
use JiagBrody\LaravelFacturaMx\Enums\InvoiceCfdiCancelTypeEnum;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Services\SaveSoapRequestResponseLogService;
use SoapClient;

trait CancelTrait
{
    private InvoiceCfdiCancelTypeEnum $cfdiCancelTypeEnum;

    private ?string $replace_uuid;

    private function cancel(InvoiceCfdiCancelTypeEnum $cancelType, ?string $replacementUUID): PacCancelResponse
    {
        $this->cfdiCancelTypeEnum = $cancelType;
        $this->replace_uuid = $replacementUUID;

        return $this->signCancelSoap();

        //UPDATE 2022-09-27: SE VA A DEJAR DE USAR ESTE METODO YA QUE VEO COMPLICADO MANTENER ALGO QUE EN EL DEMO DEL SAT NO VA A FUNCIONAR.
        //AUNQUE EN PRODUCCION LO IDEAL ES USAR "cancelSignature".
        //return $this->cancelSignatureSoap();
        //return $this->cancelSignatureQuickFinkok();
    }

    private function signCancelSoap(): PacCancelResponse
    {
        $uuids = ['UUID' => $this->invoice->invoiceCfdi->uuid, 'Motivo' => $this->cfdiCancelTypeEnum->getSatId()];
        if ($this->cfdiCancelTypeEnum === InvoiceCfdiCancelTypeEnum::NEW_WITH_ERRORS_RELATED && is_string($this->replace_uuid)) {
            $uuids['FolioSustitucion'] = $this->replace_uuid;
        }

        $uuid_ar = ['UUID' => $uuids];
        $uuids_ar = ['UUIDS' => $uuid_ar];
        $params = [
            'UUIDS' => $uuid_ar,
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
            'taxpayer_id' => $this->invoiceCompanyHelper->rfc,
            'serial' => $this->invoiceCompanyHelper->serialNumber,
        ];

        try {
            $client = new SoapClient($this->cancelUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall('sign_cancel', [$params]);

            (new SaveSoapRequestResponseLogService)->make($client, 'Finkok:sign_cancel', 'cfdi_finkok_sign_cancel');

            return $this->getResponsePac($response->sign_cancelResult);
        } catch (exception $e) {
            abort(422, $e->getMessage());
        }
    }

    /*private function cancelSignatureSoap(): array
    {
        $documentToCancel = CancelDocument::newNotExecuted($this->invoice->cfdi->uuid);

        $credential           = Credential::openFiles($this->invoiceCompanyHelper->pathCertificado,
            $this->invoiceCompanyHelper->pathKey, $this->invoiceCompanyHelper->passPhrase);
        $credentials          = Credentials::createWithPhpCfdiCredential($credential);
        $xmlCancelacion       = new XmlCancelacionHelper($credentials);
        $uuid                 = $this->invoice->cfdi->uuid;
        $solicitudCancelacion = $xmlCancelacion->signCancellation($documentToCancel);
        $params               = [
            "xml"      => $solicitudCancelacion,
            "username" => $this->usernameFinkok,
            "password" => $this->passwordFinkok,
        ];

        try {
            $client   = new SoapClient($this->cancelUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall("cancel_signature", [$params]);
        } catch (exception $e) {
            abort(422, $e->getMessage());
        }

        return $this->getResponsePac($response->cancel_signatureResult);
    }*/

    /*private function cancelSignatureQuickFinkok(): array
    {
        $documentToCancel = CancelDocument::newNotExecuted($this->invoice->cfdi->uuid);

        $credential = Credential::openFiles($this->invoiceCompanyHelper->pathCertificado, $this->invoiceCompanyHelper->pathKey,
            $this->invoiceCompanyHelper->passPhrase);

        $result       = $this->quickFinkok->cancel($credential, $documentToCancel);
        $documentInfo = $result->documents()->first();

        //Detectar errores
        $status = (int)$documentInfo->documentStatus();
        if ($status !== 201 && $status !== 202) {
            abort(422, "Codigo de error cancelación: ".$status);
        }

        $cfdiCancel = $this->invoice->cfdi->cfdiCancel()->create([
            'cancel_type'         => $this->cfdiCancelTypeEnum->value,
            'estatus_uuid'        => $documentInfo->documentStatus(),
            'estatus_cancelacion' => $documentInfo->cancellationStatus(),
            'rfc_emisor'          => $result->rfc(),
            'date'                => $result->date(),
        ]);

        $fileName = Document::getInitialFileName($cfdiCancel, 'xml', $documentInfo->uuid());
        (new DocumentGenerateService(model: $cfdiCancel, fileName: $fileName, extension: 'xml', mimeType: 'text/xml',
            fileContent: $result->voucher()))->make();

        // Trabajar con la respuesta
        $descripcion = "UUID: {$documentInfo->uuid()}<br><br>
        Estado del CFDI: {$documentInfo->documentStatus()}<br><br>
        Estado de cancelación: {$documentInfo->cancellationStatus()}
        ";

        return [
            'title'       => 'Código de estado de la solicitud de cancelación: '.$result->statusCode(),
            'description' => $descripcion,
        ];
    }*/

    /*private function respuesta201(): array
    {
        $response = [
            'sign_cancelResult' => [
                'Folios'    => [
                    'Folio' => [
                        'UUID'               => 'D820A9F4-EADD-5E10-B45C-AE9310991E9E',
                        'EstatusUUID'        => '201',
                        'EstatusCancelacion' => 'Petición de cancelación realizada exitosamente',
                    ],
                ],
                'Acuse'     => '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"><s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><CancelaCFDResponse xmlns="http://cancelacfd.sat.gob.mx"><CancelaCFDResult Fecha="2022-09-20T11:46:42" RfcEmisor="EKU9003173C9"><Folios><UUID>D820A9F4-EADD-5E10-B45C-AE9310991E9E</UUID><EstatusUUID>201</EstatusUUID></Folios><Signature Id="SelloSAT" xmlns="http://www.w3.org/2000/09/xmldsig#"><SignedInfo><CanonicalizationMethod Algorithm="http://www.w3.org/TR/2001/REC-xml-c14n-20010315"/><SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#hmac-sha512"/><Reference URI=""><Transforms><Transform Algorithm="http://www.w3.org/TR/1999/REC-xpath-19991116"><XPath>not(ancestor-or-self::*[local-name()=\'Signature\'])</XPath></Transform></Transforms><DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha512"/><DigestValue>P2lENAhvzEBJuAS1aIPcpZVjYN2A73ckFc0wkj+XPA2rp6NRIVGFEPL3qCSAwl2R2+xx1ZaItMkSuv+f5t3ZGA==</DigestValue></Reference></SignedInfo><SignatureValue>EeXPV2EIrQtZzERdWgvrVOkl5HVcBVwZChX2Q/q9W2KzliiVWTznzVNVPqzaAvGkaZqAdv6KzbPeD9fUXTpB5g==</SignatureValue><KeyInfo><KeyName>00001088888800000093</KeyName><KeyValue><RSAKeyValue><Modulus>yxMvUucuS+s3aeWTFZvJrrFWIdes7kIDJmO7DA5DP+ZTapofNt37fgeIHlTUdAVvd/fDKhfiwNSh+vbrNbD58X3UEdQor3ngb6zpjrDjgYsedckPLv6fro4DO0NXLCdALFqhN8ARyX77kYBnvIj1fOSVp401Vc3urLUtiEm16Kle3tOyWhfjgFzdK3oAIXF8oeei/GburWbJnpP+NeGaHVE5bkxLCBp5757nKVonXwzpfpEGuBp204NGkI2/jyA2EH8wyRN4yUvzjT7IJYrHng23klRDlJoRYwa98QQPdQSTpcrlNu8nLhpQdI/zMTLoNF2NiBCkQNuAMacKhnvlVw==</Modulus><Exponent>AQAB</Exponent></RSAKeyValue></KeyValue></KeyInfo></Signature></CancelaCFDResult></CancelaCFDResponse></s:Body></s:Envelope>',
                'Fecha'     => '2022-09-20T11:46:42',
                'RfcEmisor' => 'EKU9003173C9',
            ],
        ];

        return json_decode(json_encode($response));
    }*/

    private function getResponsePac($cancelResult): PacCancelResponse
    {
        if (!isset($cancelResult->Folios->Folio)) {
            abort(422, $cancelResult->CodEstatus);
        }

        $folio = $cancelResult->Folios->Folio;
        $response = new PacCancelResponse;
        $response->setUuid($folio->UUID);
        $response->setEstatusUUID($folio->EstatusUUID);
        $response->setEstatusCancelacion($folio->EstatusCancelacion);

        if ($folio->EstatusUUID === '201' || $folio->EstatusUUID === '202') {
            if ($folio->EstatusCancelacion === 'Petición de cancelación realizada exitosamente') {
                $response->setCheckProcess(true);
                $response->setAcuse($cancelResult->Acuse);

                return $response;
            }
        }

        $response->setCheckProcess(false);

        return $response;
    }
}

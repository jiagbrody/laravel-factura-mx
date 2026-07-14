<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\PacProviders\Finkok;

use JiagBrody\LaravelFacturaMx\Exceptions\InvoiceNotStampedException;
use JiagBrody\LaravelFacturaMx\Sat\PacProviders\PacRecoveryCfdiXmlResponse;
use JiagBrody\LaravelFacturaMx\Services\Document\Helpers as DocumentHelpers;

trait XmlStampedTrait
{
    private function getXmlStampedCfdiSat(): PacRecoveryCfdiXmlResponse
    {
        if ($this->invoice->invoiceCfdi === null) {
            throw InvoiceNotStampedException::forOperation('recuperar el XML timbrado');
        }

        $response = $this->recoverStampedXmlByUuid((string) $this->invoice->invoiceCfdi->uuid);

        if ($response->getCheckProcess()) {
            return $response;
        }

        // get_xml no siempre indexa el CFDI (p. ej. timbrados resueltos vía la
        // operación "stamped" responden "UUID Does not Exists"). Si aún existe
        // el XML de borrador de la factura, se consulta "stamped" con él para
        // obtener el resultado original del timbrado.
        return $this->recoverStampedXmlFromDraft();
    }

    /**
     * Recupera el XML timbrado por UUID directamente del PAC. No depende de
     * que el CFDI esté registrado localmente: también se usa cuando el PAC
     * responde "timbrado previamente" sin incluir el XML.
     */
    private function recoverStampedXmlByUuid(string $uuid): PacRecoveryCfdiXmlResponse
    {
        $params = [
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
            'taxpayer_id' => $this->invoiceCompanyHelper->rfc,
            'uuid' => $uuid,
            'invoice_type' => 'I', // Finkok solo acepta 'I' (CFDI) o 'R' (retenciones). https://wiki.finkok.com/en/home/webservices/utilerias/get_xml
        ];

        $data = $this->soapCaller()->call($this->utilitiesUrlFinkok, 'get_xml', $params, 'cfdi_finkok_get_xml');

        $xml = (string) ($data->get_xmlResult->xml ?? '');

        return $xml !== ''
            ? PacRecoveryCfdiXmlResponse::found($xml)
            : PacRecoveryCfdiXmlResponse::notFound();
    }

    /**
     * Fallback: consulta la operación "stamped" con el XML de borrador de la
     * factura (mismos parámetros que el timbrado) para recuperar el resultado
     * original cuando get_xml no encuentra el UUID.
     */
    private function recoverStampedXmlFromDraft(): PacRecoveryCfdiXmlResponse
    {
        $draftDocument = $this->invoice->xmlInvoiceDocument;

        if ($draftDocument === null) {
            return PacRecoveryCfdiXmlResponse::notFound();
        }

        $draftXmlContent = DocumentHelpers::obtainExistingDocumentFile($draftDocument);

        if ($draftXmlContent === null || trim($draftXmlContent) === '') {
            return PacRecoveryCfdiXmlResponse::notFound();
        }

        $params = [
            'xml' => $draftXmlContent,
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
        ];

        $response = $this->soapCaller()->call($this->stampUrlFinkok, 'stamped', $params, 'cfdi_finkok_stamped');

        $xml = (string) ($response->stampedResult->xml ?? '');

        return $xml !== ''
            ? PacRecoveryCfdiXmlResponse::found($xml)
            : PacRecoveryCfdiXmlResponse::notFound();
    }
}

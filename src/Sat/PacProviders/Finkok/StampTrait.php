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
        $draftCfdi = InvoiceDocument::obtainDocumentContent($xmlFile);

        $params = [
            'xml' => $draftCfdi,
            'username' => $this->usernameFinkok,
            'password' => $this->passwordFinkok,
        ];

        try {
            $client = new SoapClient($this->stampUrlFinkok, ['trace' => 1]);
            $response = $client->__soapCall('quick_stamp', [$params]);

            (new SaveSoapRequestResponseLogService)->make($client, 'Finkok:quick_stamp', 'cfdi_finkok_quick_stamp');

            return $this->setAndGetResponse($response);

        } catch (exception $e) {
            abort(422, $e->getMessage());
        }
    }

    private function setAndGetResponse($pacResponse): PacStampResponse
    {
        $response = new PacStampResponse;
        return $response->setFullResponse($pacResponse);
    }

    private function detectLogicErrorInStamp(): void
    {
        if ($this->invoice->cfdi) {
            abort(403, 'Esta factura ya se encuentra timbrada!');
        }
    }
}

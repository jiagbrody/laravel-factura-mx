<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Stamp;

use App\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PacProviderHelper;
use JiagBrody\LaravelFacturaMx\Sat\StampCfdiInterface;

class EgresoStamp extends PacProviderHelper implements StampCfdiInterface
{
    public function __construct(protected Invoice $invoice)
    {
        parent::__construct($this->invoice);
    }

    public function getPacResponse(): \App\Services\PAC\Providers\PacStampResponse
    {
        $pacResponse = $this->pacProvider->stampInvoice();

        $this->runConcrete($pacResponse);

        return $pacResponse;
    }

    private function runConcrete(\App\Services\PAC\Providers\PacStampResponse $pacResponse): void
    {
        if ($pacResponse->checkProcess) {
            $concrete = new EgresoStampConcrete($this->invoice, $pacResponse);
            $concrete
                ->removeInvoiceDraft()
                ->createCfdi()
                ->generateDocumentsFromCfdi();
        }
    }
}

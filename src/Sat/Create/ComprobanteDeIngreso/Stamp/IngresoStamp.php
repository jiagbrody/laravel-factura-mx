<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso\Stamp;

use App\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PacProviderHelper;
use JiagBrody\LaravelFacturaMx\Sat\StampCfdiInterface;

class IngresoStamp extends PacProviderHelper implements StampCfdiInterface
{
    protected IngresoStampConcrete $concrete;

    public function __construct(protected Invoice $invoice)
    {
        parent::__construct($this->invoice);
    }

    public function getPacResponse(): \App\Services\PAC\Providers\PacStampResponse
    {
        $pacResponse = $this->pacProvider->stampInvoice();
        $this->concrete = new IngresoStampConcrete($this->invoice, $pacResponse);
        $this->runConcrete($pacResponse);

        return $pacResponse;
    }

    private function runConcrete($pacResponse): void
    {
        if ($pacResponse->checkProcess) {
            $this->concrete
                ->updateInvoice()
                ->createCfdi()
                ->generateDocuments();
        }
    }
}

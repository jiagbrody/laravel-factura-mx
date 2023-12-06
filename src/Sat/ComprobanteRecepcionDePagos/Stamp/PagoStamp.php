<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\Stamp;

use App\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Helper\PacProviderHelper;
use JiagBrody\LaravelFacturaMx\Sat\StampCfdiInterface;

class PagoStamp extends PacProviderHelper implements StampCfdiInterface
{
    private PagoStampConcrete $concrete;

    public function __construct(protected Invoice $invoice)
    {
        parent::__construct($this->invoice);
    }

    public function getPacResponse(): \App\Services\PAC\Providers\PacStampResponse
    {
        $pacResponse    = $this->pacProvider->stampInvoice();
        $this->concrete = new PagoStampConcrete($this->invoice, $pacResponse);

        //103.2871008

        if ($pacResponse->checkProcess) {
            $this->concrete
                ->updateInvoice()
                ->updatePaymentDocumentsAsActive()
                ->createCfdi()
                ->generateDocuments();
        }

        return $pacResponse;
    }
}

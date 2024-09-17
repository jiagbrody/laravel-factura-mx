<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso\Stamp;

use App\Services\PAC\Providers\PacStampResponse;
use JiagBrody\LaravelFacturaMx\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\Helper\StampConcrete;

class EgresoStampConcrete extends StampConcrete
{
    public function __construct(protected Invoice $invoice, protected PacStampResponse $pacStampResponse)
    {
        parent::__construct($invoice, $pacStampResponse);
    }
}

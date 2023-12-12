<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Stamp;

use App\Models\Invoice;
use App\Services\PAC\Providers\PacStampResponse;
use JiagBrody\LaravelFacturaMx\Sat\Helper\StampConcrete;

class EgresoStampConcrete extends StampConcrete
{
    public function __construct(protected Invoice $invoice, protected PacStampResponse $pacStampResponse)
    {
        parent::__construct($invoice, $pacStampResponse);
    }
}

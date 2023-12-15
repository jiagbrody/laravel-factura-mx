<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Cancel;

use App\Models\Invoice;
use App\Services\PAC\Providers\PacCancelResponse;
use JiagBrody\LaravelFacturaMx\Sat\Helper\CancelConcrete;

final class EgresoCancelConcrete extends CancelConcrete
{
    public function __construct(protected Invoice $invoice, protected PacCancelResponse $pacCancelResponse)
    {
        parent::__construct($invoice, $pacCancelResponse);
    }
}

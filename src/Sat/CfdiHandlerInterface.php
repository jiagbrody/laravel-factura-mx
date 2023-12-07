<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use JiagBrody\LaravelFacturaMx\Models\Invoice;

interface CfdiHandlerInterface
{
    public function create();

    // public function stamp(Invoice $invoice);
    //
    // public function cancel(Invoice $invoice, $cfdiCancelTypeEnum, $UUID);
}

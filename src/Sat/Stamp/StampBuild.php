<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Stamp;

use JiagBrody\LaravelFacturaMx\Models\Invoice;

class StampBuild implements StampBuildInterface
{
    public function __construct(Invoice $invoice)
    {
    }
}

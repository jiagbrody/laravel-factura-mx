<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Helper;

use App\Models\Invoice;

interface DraftBuildInterface
{
    public function setInvoice(Invoice $invoice): self;

    public function getObjectFromComprobanteData(): \stdClass;
}

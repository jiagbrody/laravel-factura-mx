<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat;

use App\Models\Invoice;

interface CfdiHandlerInterface
{
    public function draft(int $invoiceCompanyId);

    public function stamp(Invoice $invoice);

    public function cancel(Invoice $invoice, $cfdiCancelTypeEnum, $UUID);
}

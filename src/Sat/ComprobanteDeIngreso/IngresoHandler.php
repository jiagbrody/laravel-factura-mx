<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso;

use App\Enums\InvoiceCompanyEnum;
use App\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Cancel\CancelCfdi;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Draft\IngresoDraft;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Stamp\IngresoStamp;

class IngresoHandler implements CfdiHandlerInterface
{
    public function draft(int $invoiceCompanyId): IngresoDraft
    {
        return new IngresoDraft(InvoiceCompanyEnum::from($invoiceCompanyId));
    }

    public function stamp(Invoice $invoice): IngresoStamp
    {
        return new IngresoStamp($invoice);
    }

    public function cancel(Invoice $invoice, $cfdiCancelTypeEnum, $UUID): CancelCfdi
    {
        return new CancelCfdi($invoice, $cfdiCancelTypeEnum, $UUID);
    }
}

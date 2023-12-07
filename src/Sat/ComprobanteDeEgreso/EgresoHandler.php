<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso;

use App\Enums\InvoiceCompanyEnum;
use App\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Cancel\EgresoCancel;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Create\EgresoCreate;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Stamp\EgresoStamp;

class EgresoHandler implements CfdiHandlerInterface
{
    public function create(): EgresoCreate
    {
        // return new EgresoDraft(InvoiceCompanyEnum::from($invoiceCompanyId));
    }

    public function stamp(Invoice $invoice): EgresoStamp
    {
        return new EgresoStamp($invoice);
    }

    public function cancel(Invoice $invoice, $cfdiCancelTypeEnum, $UUID): EgresoCancel
    {
        return new EgresoCancel($invoice, $cfdiCancelTypeEnum, $UUID);
    }
}

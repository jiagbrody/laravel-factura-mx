<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso;

use App\Enums\InvoiceCompanyEnum;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso\Cancel\EgresoCancel;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso\Stamp\EgresoStamp;

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

    public function custom(InvoiceCompany $company)
    {
        // TODO: Implement custom() method.
    }

    public function fromComprobante(array $comprobante, Collection $products)
    {
        // TODO: Implement fromComprobante() method.
    }
}

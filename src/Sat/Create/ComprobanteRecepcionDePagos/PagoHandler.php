<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos;

use App\Enums\InvoiceCompanyEnum;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos\Cancel\PagoCancel;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos\Create\PagoCreate;
use JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteRecepcionDePagos\Stamp\PagoStamp;

class PagoHandler implements CfdiHandlerInterface
{
    public function create(): PagoCreate
    {
        // return new PagoDraft(InvoiceCompanyEnum::from($invoiceCompanyId));
    }

    public function stamp(Invoice $invoice): PagoStamp
    {
        return new PagoStamp($invoice);
    }

    public function cancel(Invoice $invoice, $cfdiCancelTypeEnum, $UUID): PagoCancel
    {
        return new PagoCancel($invoice, $cfdiCancelTypeEnum, $UUID);
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

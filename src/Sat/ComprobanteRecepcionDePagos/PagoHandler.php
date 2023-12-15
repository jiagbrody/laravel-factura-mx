<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos;

use App\Enums\InvoiceCompanyEnum;
use App\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\Cancel\PagoCancel;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\Create\PagoCreate;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteRecepcionDePagos\Stamp\PagoStamp;

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
}

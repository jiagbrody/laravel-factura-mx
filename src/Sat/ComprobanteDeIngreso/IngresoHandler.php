<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso;

use App\Enums\InvoiceCompanyEnum;
use App\Models\Invoice;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;
use JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeIngreso\Create\IngresoCreate;

class IngresoHandler implements CfdiHandlerInterface
{
    public function create(): IngresoCreate
    {
        return new IngresoCreate();
    }

    // public function stamp(Invoice $invoice): IngresoStamp
    // {
    //     return new IngresoStamp($invoice);
    // }

    // public function cancel(Invoice $invoice, $cfdiCancelTypeEnum, $UUID): CancelCfdi
    // {
    //     return new CancelCfdi($invoice, $cfdiCancelTypeEnum, $UUID);
    // }
}

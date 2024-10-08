<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;
use JiagBrody\LaravelFacturaMx\Sat\InvoiceSatData\ComprobanteAtributos;

class IngresoHandler implements CfdiHandlerInterface
{
    public function custom(InvoiceCompany $company): IngresoCreateConcrete
    {
        return new IngresoCreateConcrete($company);
    }

    public function fromComprobante(ComprobanteAtributos|array $comprobante, Collection $products): IngresoCreateConcrete
    {
        return (new IngresoCreateConcrete(new InvoiceCompany))
            ->addAtributos($comprobante)
            ->addReceptor($comprobante['Receptor'])
            ->addConceptos($products)
            ->addRelacionados($comprobante['CfdiRelacionados'])
            ->addComplementoImpuestosLocales(impuestosLocales: $comprobante['Complemento']['ImpuestosLocales']);
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

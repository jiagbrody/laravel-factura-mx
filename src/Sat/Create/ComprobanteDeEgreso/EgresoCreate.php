<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso;

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\Create\Helpers\GenericCreator;

readonly class EgresoCreate
{
    public function custom(InvoiceCompany $company): GenericCreator
    {
        return new GenericCreator($company);
    }

    // public function fromExistingInvoiceToRelate(Invoice $invoice): EgresoCreateConcrete
    // {
    //     return (new EgresoCreateConcrete($invoice->invoiceCompany))
    //         ->addAtributos($comprobante)
    //         ->addReceptor($comprobante['Receptor'])
    //         ->addConceptos($products)
    //         ->addRelacionados($comprobante['CfdiRelacionados']);
    // }
}

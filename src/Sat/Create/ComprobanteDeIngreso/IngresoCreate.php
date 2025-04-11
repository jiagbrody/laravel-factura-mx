<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;

class IngresoCreate implements CfdiHandlerInterface
{
    public function custom(InvoiceCompany $company): IngresoCreateConcrete
    {
        return new IngresoCreateConcrete($company);
    }

    // public function fromComprobante(ComprobanteAtributos|array $comprobante, Collection $products): IngresoCreateConcrete
    // {
    //     return (new IngresoCreateConcrete(new InvoiceCompany))
    //         ->addAtributos($comprobante)
    //         ->addReceptor($comprobante['Receptor'])
    //         ->addConceptos($products)
    //         ->addRelacionados($comprobante['CfdiRelacionados'])
    //         ->addComplementoImpuestosLocales(impuestosLocales: $comprobante['Complemento']['ImpuestosLocales']);
    // }
}

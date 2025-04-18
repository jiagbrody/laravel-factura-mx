<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\CfdiHandlerInterface;
use JiagBrody\LaravelFacturaMx\Sat\Create\Helpers\GenericCreator;

class IngresoCreate implements CfdiHandlerInterface
{
    public function custom(InvoiceCompany $company): GenericCreator
    {
        return new GenericCreator($company);
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

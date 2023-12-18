<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeIngreso;

use Illuminate\Database\Eloquent\Collection;
use JiagBrody\LaravelFacturaMx\Models\InvoiceCompany;
use JiagBrody\LaravelFacturaMx\Sat\DraftSatInterface;

readonly class IngresoCreate implements DraftSatInterface
{
    public function custom(InvoiceCompany $company): IngresoCreateConcrete
    {
        return new IngresoCreateConcrete($company);
    }

    public function fromComprobante(array $comprobante, Collection $products): IngresoCreateConcrete
    {
        /*foreach ($comprobante as $key => $value) {
            if ($value === null) {
                unset($comprobante[$key]);
            }
        }*/

        return (new IngresoCreateConcrete(new InvoiceCompany))->addAtributos($comprobante)
            ->addReceptor($comprobante['Receptor'])
            ->addConceptos($products)
            ->addRelacionados($comprobante['CfdiRelacionados'])
            ->addComplementoImpuestosLocales(impuestosLocales: $comprobante['Complemento']['ImpuestosLocales']);
    }
}

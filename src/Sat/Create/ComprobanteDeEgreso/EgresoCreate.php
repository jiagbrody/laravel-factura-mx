<?php

declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\Create\ComprobanteDeEgreso;

use App\Enums\InvoiceCompanyEnum;
use JiagBrody\LaravelFacturaMx\Sat\GettingSatCfdiInterface;

readonly class EgresoCreate implements GettingSatCfdiInterface
{
    public function __construct(protected InvoiceCompanyEnum $invoiceCompanyEnum)
    {
    }

    public function custom($company): EgresoCreateConcrete
    {
        return new EgresoCreateConcrete($this->invoiceCompanyEnum);
    }

    public function fromComprobante(array $comprobante, $products = []): EgresoCreateConcrete
    {
        $concept = (! empty($products)) ? $products : $comprobante['Conceptos']['Concepto'][0];

        return (new EgresoCreateConcrete($this->invoiceCompanyEnum))
            ->addAtributos($comprobante)
            ->addReceptor($comprobante['Receptor'])
            ->addConceptos($concept)
            ->addRelacionados($comprobante['CfdiRelacionados']);
    }
}

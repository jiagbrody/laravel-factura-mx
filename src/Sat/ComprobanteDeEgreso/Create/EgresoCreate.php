<?php declare(strict_types=1);

namespace JiagBrody\LaravelFacturaMx\Sat\ComprobanteDeEgreso\Create;

use App\Enums\InvoiceCompanyEnum;
use JiagBrody\LaravelFacturaMx\Sat\DraftSatInterface;

readonly class EgresoCreate implements DraftSatInterface
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
        $concept = (!empty($products)) ? $products : $comprobante['Conceptos']['Concepto'][0];

        return (new EgresoCreateConcrete($this->invoiceCompanyEnum))
            ->addAtributos($comprobante)
            ->addReceptor($comprobante['Receptor'])
            ->addConceptos($concept)
            ->addRelacionados($comprobante['CfdiRelacionados']);
    }
}
